<?php

declare(strict_types=1);

namespace api\models;

use api\models\common\Uuid;

class Process
{
    private static string $pid = '';

    public static function getPid(): string
    {
        if (!self::$pid) {
            self::init();
        }

        return self::$pid;
    }

    public static function init(): void
    {
        self::$pid = Uuid::uuid4();
    }
}