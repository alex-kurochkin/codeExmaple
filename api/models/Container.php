<?php

declare(strict_types=1);

namespace api\models;

use Yii;

class Container
{
    public static function get(string $className, array $params = [], array $config = []): object
    {
        return Yii::$container->get($className, $params, $config);
    }

    public static function invoke(callable $callback, $params = [])
    {
        return Yii::$container->invoke($callback, $params);
    }
}