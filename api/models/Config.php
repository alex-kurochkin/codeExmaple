<?php

declare(strict_types=1);

namespace api\models;

use RuntimeException;
use Yii;

class Config
{
    public static function getAppName(): string
    {
        return Yii::$app->params['app.name'];
    }

    public static function getSupportEmail(): string
    {
        return Yii::$app->params['supportEmail'];
    }

    public static function getCorsOrigin(): string
    {
        return Yii::$app->params['app.corsOrigin'];
    }

    public static function getServerId(): int
    {
        $serverId = Yii::$app->params['app.serverId'];

        if (!$serverId) {
            throw new RuntimeException('Server misconfigured: server id wrong: ' . $serverId);
        }

        return $serverId;
    }

    public static function getNeedVerifyEmail(): bool
    {
        return Yii::$app->params['app.needVerifyEmail'];
    }

    public static function getDefaultCurrency(): bool
    {
        return Yii::$app->params['app.defaultCurrency'];
    }

    public static function getUserPasswordMinLength(): int
    {
        return Yii::$app->params['user.passwordMinLength'];
    }

    public static function getUserPasswordResetTokenExpire(): int
    {
        return Yii::$app->params['user.passwordResetTokenExpire'];
    }

    public static function getUserLoginIdleLimit(): int
    {
        return Yii::$app->params['app.userLoginIdleLimit'];
    }

    public static function getUser2faIdleLimit(): int
    {
        return Yii::$app->params['app.user2faIdleLimit'];
    }

    public static function getUserSentPhoneCodeNoDelayAttempt(): int
    {
        return Yii::$app->params['app.userSentPhoneCodeNoDelayAttempt'];
    }

    public static function getUserSentPhoneCodeDelay(): int
    {
        return Yii::$app->params['app.userSentPhoneCodeDelay'];
    }

    /** Twilio (sms) */

    public static function getTwilioSid(): string
    {
        return Yii::$app->params['twilio.sid'];
    }

    public static function getTwilioToken(): string
    {
        return Yii::$app->params['twilio.token'];
    }

    public static function getTwilioNumber(): string
    {
        return Yii::$app->params['twilio.number'];
    }

    /** Mailinblue (email notificator) */

    public static function getSendinServerUrl(): string
    {
        return Yii::$app->params['mailinblue.serverUrl'];
    }

    public static function getSendinApiKey(): string
    {
        return Yii::$app->params['mailinblue.apiKey'];
    }

    /** Notificator (select notificator) */

    public static function getNotificatorClassName(): string
    {
        return Yii::$app->params['notificator.className'];
    }

    /** Enot */

    public static function getEnotId(): string
    {
        return Yii::$app->params['enot.id'];
    }

    public static function getEnotSecretWord(): string
    {
        return Yii::$app->params['enot.secretWord'];
    }

    public static function getEnotSecretWordResult(): string
    {
        return Yii::$app->params['enot.secretWordResult'];
    }
}