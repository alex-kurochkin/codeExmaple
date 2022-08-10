<?php

declare(strict_types=1);

namespace api\models\logs\target;

use api\controllers\params\message\MessageParams;

class TelegramErrorLogTarget
{
    public static function send($message): void
    {
        $params = MessageParams::getParamsInstance('error');
        $params->load(
            [
                'channel' => 'error',
                'format' => 'Error',
                'error' => $message,
            ],
            ''
        );

//        $channel = new Channel();
//        $chats = new Chat();

//        (new TelegramMessage(new DynamicConfig(), $channel, $chats))->processUserMessage($params);
    }
}