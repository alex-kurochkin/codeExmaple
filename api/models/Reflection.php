<?php

declare(strict_types=1);

namespace api\models;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class Reflection
{
    public static function getClassShortName($c): string
    {
        try {
            return (new ReflectionClass($c))->getShortName();
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    public static function getMethodShortName($name): string
    {
        try {
            $method = new ReflectionMethod($name);

            $className = $method->getDeclaringClass()->getShortName();
            $methodName = $method->getShortName();

            return $className . '::' . $methodName;
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}