<?php

declare(strict_types=1);

namespace api\models;

class Server
{
    public const ID_CHINA = 1;
    public const ID_MAIN = 2;

    public static function isProd(): bool
    {
        return YII_ENV === 'prod';
    }
}