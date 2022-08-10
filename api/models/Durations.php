<?php

declare(strict_types=1);

namespace api\models;

use RuntimeException;

class Durations
{
    private static array $durations = [
        'hour' => 3600,
        '2h' => 7200,
        '4h' => 14400,
        '6h' => 21600,
        '8h' => 28800,
        '10h' => 36000,
        '12h' => 43200,
        'day' => 86400,
        'threeDays' => 259200,
        'week' => 604800,
        'month' => 2592000,
        'halfYear' => 15811200,
        'year' => 31536000,
    ];

    public static function getDurationName(int $seconds)
    {
        $durations = array_flip(self::$durations);
        if (array_key_exists($seconds, $durations)) {
            return $durations[$seconds];
        }

        throw new RuntimeException('Unknown duration name for seconds: ' . $seconds);
    }

    public static function getDurationSeconds(string $name): int
    {
        if (array_key_exists($name, self::$durations)) {
            return self::$durations[$name];
        }

        throw new RuntimeException('Unknown duration name: ' . $name);
    }
}