<?php

declare(strict_types=1);

namespace api\models;

trait ModelArrayImportOneTrait
{
    private static function arrayValuesToInt(array &$array): array
    {
        foreach ($array as $k => &$value) {
            if (is_array($value)) {
                $value = self::arrayValuesToInt($value);
                continue;
            }
            $value = (int)$value;
        }

        return $array;
    }

    public function importOne($array, bool $withInheritance = false): Model
    {
        $model = clone $this;
        foreach ($array as $k => $v) {
            $model->$k = $v;
        }

        return $model;
    }
}