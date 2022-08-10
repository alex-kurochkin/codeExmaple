<?php

declare(strict_types=1);

namespace api\models;

class StringTransform
{
    public static function camelToSnake(string $string): string
    {
        return strtolower(ltrim(preg_replace('/([A-Z])/', '_\\1', $string), '_'));
    }
}