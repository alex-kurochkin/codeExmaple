<?php

declare(strict_types=1);

namespace api\services\notify\user;

use api\models\Config;
use api\models\logs\MailLog;
use api\models\user\User;
use api\services\notify\Notificator;
use RuntimeException;

class NotifyUserEmailService
{
    public static function sendFastLogin(User $user): void
    {
        self::logInfo('Account registration', $user);

        if (!self::send($user, 'fastLogin')) {
            self::handleError('Account registration send mail failed');
        }
    }

    private static function logInfo(string $message, User $user): void
    {
        MailLog::info($message, ['User id' => $user->id, 'email' => $user->email, 'user' => $user]);
    }

    private static function send(User $user, string $emailTarget): bool
    {
        $notificator = Notificator::getNotificator();

        $mailNotify = $notificator->getNotify();

        // @todo drop this code after Config::getServerId real value implemented
        $cors = Config::getCorsOrigin();
        $sid = false !== strpos($cors, "yidongzhuanxian") ? '1' : '2';
        $serverId = 'server' . $sid;

//        $serverId = 'server' . Config::getServerId();

        $mailNotify->setTo([$user->email => $user->username]);
        $mailNotify->setFrom([Config::getSupportEmail(), Config::getAppName()]);
        $mailNotify->renderSubject($user, $serverId, $emailTarget);
        $mailNotify->renderHtml($user, $serverId, $emailTarget);
        $mailNotify->renderText($user, $serverId, $emailTarget);

        return $notificator->send($mailNotify);
    }

    private static function handleError($message): void
    {
        MailLog::error($message);
        throw new RuntimeException($message);
    }

    public static function sendPasswordResetToken(User $user): void
    {
        self::logInfo('Password reset', $user);

        if (!self::send($user, 'passwordResetToken')) {
            self::handleError('Password reset send mail failed');
        }
    }

    public static function sendEmailVerify(User $user): void
    {
        self::logInfo('Email verification', $user);

        if (!self::send($user, 'emailVerify')) {
            self::handleError('Email verification send mail failed');
        }
    }

    public static function send2faLogin(User $user): void
    {
        self::logInfo('2fa login', $user);

        if (!self::send($user, 'login2fa')) {
            self::handleError('2fa login send mail failed');
        }
    }
}