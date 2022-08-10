<?php

declare(strict_types=1);

namespace api\models\message\format;

use api\controllers\params\message\MessageParams;
use api\models\user\User;

class CooperationOfferFormat extends Format
{
    public function format(MessageParams $message): string
    {
        $user = '<Guest>';
        if ($message->userId) {
            $user = User::get($message->userId)->email . ' [' . $message->userId . ']';
        }

        return 'Server: ' . $message->server . PHP_EOL
            . 'User: ' . $user . PHP_EOL
            . 'Messenger: ' . $message->messenger . PHP_EOL
            . 'Username: ' . $message->username . PHP_EOL
            . 'Message: ' . $message->message;
    }
}