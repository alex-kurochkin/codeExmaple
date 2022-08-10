<?php

declare(strict_types=1);

namespace api\models\dynamicConfig\ars;

use api\models\Ar;
use api\models\dynamicConfig\DynamicConfig;

/**
 * Class DynamicConfigAr
 * @package api\models\dynamicConfig\ars
 * @property string $scope [varchar(50)]
 * @property string $key [varchar(255)]
 * @property int|float|string $value [blob]
 * @property int $expire [int(10) unsigned]
 */
class DynamicConfigAr extends Ar
{
    public static function tableName(): string
    {
        return 'DynamicConfig';
    }

    public static function primaryKey(): ?array
    {
        return ['scope', 'key'];
    }

    public static function getModelName(): string
    {
        return DynamicConfig::class;
    }

    public function set(string $scope, string $key, $value, int $expire = null): void
    {
        $this->scope = $scope;
        $this->key = $key;
        $this->value = $value;
        $this->expire = $expire;
    }

    public function get(string $scope, string $key = null): array
    {
        $params = ['scope' => $scope];
        if ($key) {
            $params['key'] = $key;
        }

        return self::find()->where($params)->all();
    }

    public function deleteExpired(): void
    {
        self::deleteAll('expire <= ' . time());
    }

    public function search(string $scope, string $search): array
    {
        return self::find()
            ->where(['scope' => $scope])
            ->andWhere(['like', 'key', $search, false])
            ->asArray()
            ->all();
    }

    public function searchByValue(string $scope, $value): array
    {
        return self::find()
            ->where(['scope' => $scope, 'value' => $value])
            ->asArray()
            ->all();
    }
}