<?php

declare(strict_types=1);

namespace api\models;

use ReflectionClass;

abstract class Model// implements \IteratorAggregate
{
    protected Ar $ar;

    public static function existsBy(string $field, $value): bool
    {
        /** @var Ar $arName */
        $arName = self::getArName();
        return $arName::existsBy($field, $value);
    }

    private static function getArName(): string
    {
        $reflect = new ReflectionClass(static::class);
        $short = $reflect->getShortName();
        $ns = $reflect->getNamespaceName();
        /** @var Ar $arName */
        return $ns . '\ars\\' . $short . 'Ar';
    }

    /**
     * @param int $id
     * @param bool $throwException
     * @return static
     * @throws exception\NotFoundException
     */
    public static function get(int $id, bool $throwException = true): ?self
    {
        /** @var Ar $arName */
        $arName = self::getArName();
        $modelName = $arName::getModelName() ?: static::class;

        if ($ar = $arName::findById($id, $throwException)) {
            return (new $modelName)->importOne($ar);
        }

        return null;
    }

    /**
     * @param Ar $ar
     * @return static
     */
    protected function importOne(Ar $ar): self
    {
        $modelName = $ar::getModelName() ?: static::class;
        $model = new $modelName;

        $model->ar = $ar;

        foreach ($ar->export() as $propName => $propValue) {
            if (!property_exists($model, $propName)) {
                continue;
            }

            if (is_scalar($propValue)) {
                $model->$propName = $propValue;
                continue;
            }

            if (null !== $propValue && 'json' === $ar->getType($propName)) {
                $model->$propName = (object)$propValue;
                continue;
            }

            if (is_array($propValue)) { // Ar[]
                $model->$propName = $this->importMany($propValue);
                continue;
            }

            if ($propValue instanceof Ar) {
                $model->$propName = $this->importOne($propValue);
                continue;
            }

            $model->$propName = $propValue;
        }

        return $model;
    }

    protected function importMany(array $ars, ?string $index = null): array
    {
        $many = [];
        foreach ($ars as $ar) {
            if (is_array($ar)) {
                $many[] = $this->importOneFromArray($ar);
                continue;
            }

            if (null === $index) {
                $many[] = $this->importOne($ar);
                continue;
            }

            $many[$ar->$index] = $this->importOne($ar);
        }

        return $many;
    }

    protected function importOneFromArray(array $array): self
    {
        $modelName = static::class;
        $model = new $modelName;

        foreach ($array as $propName => $propValue) {
            if (!property_exists($model, $propName)) {
                continue;
            }

            if (is_scalar($propValue)) {
                $model->$propName = $propValue;
                continue;
            }

            if (is_array($propValue)) {
                $model->$propName = $this->importMany($propValue);
                continue;
            }

            if ($propValue instanceof Ar) {
                $model->$propName = $this->importOne($propValue);
                continue;
            }

            $model->$propName = $propValue;
        }

        return $model;
    }

    public function getPrimaryKeyValue()
    {
        $primaryKeyName = $this->getPrimaryKeyName();

        return $this->$primaryKeyName;
    }

    /**
     * If you need to override the primary key, override this method in inheritance class
     * @return string
     */
    public function getPrimaryKeyName(): string
    {
        return 'id';
    }

    /**
     * This method work the same as this::save() but create new Ar
     *  and returns new id value.
     * @return int
     */
    public function save(): int
    {
        $primaryKeyName = $this->getPrimaryKeyName();

        $arName = self::getArName();
        $this->ar = new $arName;

        $this->ar->importModel($this);
        $this->ar->save();
        $this->$primaryKeyName = $this->ar->$primaryKeyName;

        return $this->$primaryKeyName;
    }

    public function update(): self
    {
        $this->ar->importModel($this);
        $this->ar->save();
        return $this;
    }

    public function toArray(): array
    {
        $array = [];
        foreach ($this as $k => $v) {
            if (!is_scalar($v)) {
                continue;
            }

            $array[$k] = $v;
        }

        return $array;
    }

    public function reset(): void
    {
        /** @var Ar $arName */
        $arName = self::getArName();
        (new $arName)->truncate();
    }

    public function markDeletedAll(): void
    {
        /** @var Ar $arName */
        $arName = self::getArName();
        (new $arName)->markDeleteAll();
    }

    final public function getById(int $id): self
    {
        /** @var Ar $arName */
        $arName = self::getArName();
        return $this->importOne($arName::findById($id));
    }
}