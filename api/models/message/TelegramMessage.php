<?php

declare(strict_types=1);

namespace api\models\message;

use api\controllers\params\message\MessageParams;
use api\models\dynamicConfig\DynamicConfig;
use api\models\logs\MessengerLog;
use api\models\message\format\Format;
use api\models\message\telegram\CallbackQuery;
use api\models\message\telegram\ChatMessage;
use api\models\message\telegram\Command;
use api\models\messenger\tg\Tg;
use stdClass;

class TelegramMessage extends Message
{
    protected DynamicConfig $dynamicConfig;
    protected Channel $channel;
    protected Chat $chats;

    protected string $configScope = 'telegram';

    protected string $token;

    public function __construct(DynamicConfig $dynamicConfig, Channel $channel, Chat $chats)
    {
        $this->dynamicConfig = $dynamicConfig;
        $this->channel = $channel;
        $this->chats = $chats;

        $this->token = $this->dynamicConfig->get($this->configScope, 'token') ?? '';
    }

    public function processChat(): void
    {
        $inputMessage = Tg::getMessage();

        MessengerLog::info('Process chat', ['inputMessage' => $inputMessage]);

        if (property_exists($inputMessage, 'callback_query')) {
            (new CallbackQuery($this->dynamicConfig, $this->channel, $this->chats))->process($inputMessage);
            return;
        }

        if (
            property_exists($inputMessage, 'message')
            && 'bot_command' === $inputMessage->message->entities[0]->type
        ) {
            (new Command($this->dynamicConfig, $this->channel, $this->chats))->process($inputMessage);
            return;
        }

        (new ChatMessage($this->dynamicConfig, $this->channel, $this->chats))->process($inputMessage);
    }

    public function processUserMessage(MessageParams $message): void
    {
        if (!$this->token) {
            return;
        }

        MessengerLog::info('Process user message', ['message' => $message]);

        $format = Format::getFormat($message->format);

        $outputMessage = $format->format($message);

        $channel = $this->channel->getByName($message->channel);

        $telegram = new Tg($channel->token);
        if ($outputMessage) {
            $chats = $this->chats->getChats($channel->id);
            foreach ($chats as $chat) {
                $telegram->send($chat->chatId, $outputMessage);
            }
        }
    }

    protected function process(stdClass $inputMessage): void
    {
    }

    protected function getTokenByChatId(int $chatId): ?string
    {
        $chat = $this->chats->getChatByChatId($chatId);
        return $chat ? $this->channel->getById($chat->channelId)->token : null;
    }
}