<?php

declare(strict_types=1);

namespace api\models\message\telegram;

use api\models\message\TelegramMessage;
use api\models\messenger\tg\Tg;
use stdClass;

class ChatMessage extends TelegramMessage
{
    protected function process(stdClass $inputMessage): void
    {
        if (!$this->token) {
            return;
        }

        $chatId = (int)$inputMessage->message->chat->id;
        $outputMessage = 'Glad to see you';

        $token = $this->getTokenByChatId($chatId);
        $telegram = new Tg($token);
        $telegram->send($chatId, $outputMessage);
    }
}