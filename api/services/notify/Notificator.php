<?php

declare(strict_types=1);

namespace api\services\notify;

use api\models\Config;
use api\services\notify\api\MailNotify;

abstract class Notificator
{
    public static function getNotificator(): self
    {
        $c = Config::getNotificatorClassName();
        return new $c;
    }

    abstract public function send(MailNotify $mail);

    public function getNotify(): MailNotify
    {
        $c = static::NOTIFY_CLASSNAME;
        return new $c;
    }
}