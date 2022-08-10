<?php

declare(strict_types=1);

namespace api\models\message\telegram;

use api\models\Json;
use api\models\message\TelegramMessage;
use api\models\messenger\tg\Tg;
use stdClass;

class CallbackQuery extends TelegramMessage
{
    protected function process(stdClass $inputMessage): void
    {
        if (!$this->token) {
            return;
        }

        $callbackQuery = $inputMessage->callback_query;
        $chatId = (int)$callbackQuery->from->id;

        $data = Json::decode($callbackQuery->data);

        $keyboard = '';

        switch ($data->action) {
            case 'subscribe':
                $channel = $this->channel->getById($data->channelId);

                if ($this->chats->isChatSubscribedToChannel($data->channelId, $chatId)) {
                    $outputMessage = 'You are always subscribed to "' . ucfirst($channel->name) . '" channel.' . PHP_EOL
                        . 'To show channel list use command /channels ' . PHP_EOL;
                    break;
                }

                $this->chats->subscribe($chatId, $data->channelId);
                $outputMessage = 'You are subscribed to "' . ucfirst($channel->name) . '" channel.' . PHP_EOL
                    . 'To show channel list use command /channels ' . PHP_EOL;
                $keyboard = 'DEL';
                break;
            case 'unsubscribe':
                $channel = $this->channel->getById($data->channelId);

                if (!$this->chats->isChatSubscribedToChannel($data->channelId, $chatId)) {
                    $outputMessage = 'You are not subscribed to "' . ucfirst($channel->name) . '" channel.' . PHP_EOL
                        . 'To show channel list use command /channels ' . PHP_EOL;
                    break;
                }

                $this->chats->unsubscribe($chatId, $data->channelId);
                $outputMessage = 'You are left "' . ucfirst($channel->name) . '" channel.' . PHP_EOL
                    . 'To show channel list use command /channels ' . PHP_EOL;
                $keyboard = 'DEL';
                break;
            default:
                $outputMessage = 'Unknown action';
        }

        $token = $this->getTokenByChatId($chatId);
        $telegram = new Tg($token);
        $telegram->send($chatId, $outputMessage, $keyboard);
    }
}