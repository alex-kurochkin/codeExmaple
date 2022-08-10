<?php

declare(strict_types=1);

namespace api\models\maintenance;

class CountrySynonym
{
    private static array $synonyms = [
        'USA' => 'United States',
    ];

    public static function getName(string $name): string
    {
        if (array_key_exists($name, self::$synonyms)) {
            return self::$synonyms[$name];
        }

        return $name;
    }
}