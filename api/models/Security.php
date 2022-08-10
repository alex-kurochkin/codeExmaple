<?php

declare(strict_types=1);

namespace api\models;

use stdClass;
use Yii;

class Security
{
    public static function generateRandomString($length = 32): string
    {
        return Yii::$app->security->generateRandomString($length);
    }

    public static function generatePasswordHash(string $password): string
    {
        return Yii::$app->security->generatePasswordHash($password);
    }

    public static function validatePassword($password, $passwordHash): bool
    {
        return Yii::$app->security->validatePassword($password, $passwordHash);
    }

    public static function getHttpAuthorization(): ?string
    {
        return getallheaders()['Authorization'] ?? null;
    }

    public static function getClientInfo(): stdClass
    {
        $headers = getallheaders();

        return (object)[
            'ip' => $headers['x-user-ip'] ?? null,
            'platform' => $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? null,
            'mobile' => $_SERVER['HTTP_SEC_CH_UA_MOBILE'] ?? null,
            'sec' => $_SERVER['HTTP_SEC_CH_UA'] ?? null,
            'user-agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ];
    }
}