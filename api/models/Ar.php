<?php

declare(strict_types=1);

namespace api\models;

use api\models\exception\NotFoundException;
use InvalidArgumentException;
use LogicException;
use stdClass;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Transaction;

abstract class Ar extends ActiveRecord
{
    public const DATE_FORMAT = 'Y-m-d';
    public const DATETIME_FORMAT = 'Y-m-d H:i:s';

    protected static array $readOnlyProperties = [];

    protected static array $map = [];

    protected static array $types = [];
    private static Connection $connection;

    abstract public static function getModelName(): string;

    public static function beginTransaction(): Transaction
    {
        return self::getDb()->beginTransaction();
    }

    public static function getDb(): Connection
    {
        if (!isset(self::$connection)) {
            self::$connection = parent::getDb();
        }

        return self::$connection;
    }

    /**
     * @param int $id
     * @param bool $throwException
     * @return static
     * @throws NotFoundException
     */
    public static function findById(int $id, bool $throwException = true): ?self
    {
        if ($throwException) {
            return self::findOneOrFail(['id' => $id]);
        }

        return self::findOne(['id' => $id]);
    }

    public static function findOneOrFail(array $params): self
    {
        if (null === $found = static::findOne($params)) {
            Log::error('Not found [' . static::class . ']', $params);
            throw new NotFoundException(
                'Not found [' . static::class . ']: ' . json_encode($params, JSON_THROW_ON_ERROR)
            );
        }

        return $found;
    }

    public static function existsBy(string $field, $value): bool
    {
        return self::find()->where([$field => $value])->exists();
    }

    public static function getOrderDirectionConst(string $orderDirection): int
    {
        return 'asc' === $orderDirection ? SORT_ASC : SORT_DESC;
    }

    public static function escapeColumnName(string $columnName): string
    {
        return '`' . $columnName . '`';
    }

    protected static function getSqlPeriodFormats(string $period): string
    {
        switch ($period) {
            case 'day':
                return '%Y-%m-%d %H';
            case 'week':
            case 'month':
                return '%Y-%m-%d';
            case 'year':
                return '%Y-%m';
        }

        throw new InvalidArgumentException('Unknown period: ' . $period);
    }

    protected static function getPeriodStartDate(string $period): string
    {
        switch ($period) {
            case '':
                return '';
            case 'day':
                return date('Y-m-d 00:00:00');
            case 'week':
                return date('Y-m-d 00:00:00', strtotime('this week'));
            case 'month':
                return date('Y-m-01 00:00:00');
            case 'year':
                return date('Y-01-01 00:00:00');
        }

        throw new LogicException('Unknown period: ' . $period);
    }

    public function importModel(Model $model): void
    {
        $definedVariables = (array)$model;

        foreach (static::$map as $snake => $camel) {
            if (!property_exists($model, $camel)) {
                continue;
            }

            /**
             * Yes, it's a hack.
             * We can't call typed variables till it's not initialized
             * but isset() can't show different between uninitialised and null
             */
            if (/*!property_exists($model, $camel) && */ !array_key_exists($camel, $definedVariables)) {
                continue;
            }

            if ('json' === $this->getType($camel)) {
                $this->$snake = $model->$camel;
                continue;
            }

            if (is_array($model->$camel) || is_object($model->$camel)) {
                continue;
            }

            if ($this->isReadOnly($snake)) {
                continue;
            }

            $this->$snake = $model->$camel;
        }
    }

    public function getType(string $propName): string
    {
        return (string)(static::$types[$propName] ?? '');
    }

    private function isReadOnly(string $propertyName): bool
    {
        if (in_array($propertyName, static::$readOnlyProperties, true)) {
            return true;
        }

        return false;
    }

    public function importValues(array $values): void
    {
        foreach (static::$map as $snake => $camel) {
            if (!array_key_exists($camel, $values)) {
                continue;
            }

            $this->$snake = $values[$camel];
        }
    }

    public function export(): stdClass
    {
        $values = [];
        foreach (static::$map as $snake => $camel) {
            $values[$camel] = $this->$snake;
        }

        return (object)$values;
    }

    public function truncate(): void
    {
        Yii::$app->db->createCommand()->truncateTable(static::tableName())->execute();
    }

    public function markDeleteAll(): void
    {
        Yii::$app->db->createCommand()->update(static::tableName(), ['deleted' => 1], '')->execute();
    }
}