<?php

declare(strict_types=1);

namespace api\models\message\telegram;

use api\models\message\TelegramMessage;
use api\models\messenger\tg\Tg;
use stdClass;

class Command extends TelegramMessage
{
    protected function process(stdClass $inputMessage): void
    {
        $chatId = (int)$inputMessage->message->chat->id;

        $outputMessage = '';
        $menu = '';

        if ($inputMessage && $command = $inputMessage->message->text) {
//            $userTgId = $inputMessage->inputMessage->from->id;

            $command = trim($command);

            switch ($command) {
                case '/start':
                    $outputMessage = 'Welcome!' . PHP_EOL
                        . 'Select channel you want join.' . PHP_EOL
                        . 'You can call /channels to show it any time.' . PHP_EOL . PHP_EOL
                        . 'Channels:' . PHP_EOL;

                    $menu = $this->getChannelsMenu('subscribe');
                    break;
                case '/menu':
                    $outputMessage = 'Menu.';

                    $menu = [
                        'keyboard' => [
                            ['/help', '/ping', '/list', '/leave'],
                        ],
//                        'keyboard' =>
//                            [
//                                [
//                                    ['text' => 'Main', 'request_contact' => true],
//                                    ['text' => 'Second', 'request_location' => true]
//                                ]
//                            ]
//                        'keyboard' => [
//                            ['7', '8', '9'],
//                            ['4', '5', '6'],
//                            ['1', '2', '3'],
//                            ['0']
//                        ]
                    ];
                    break;
                case '/channels':
                case '/list':
                    $outputMessage = 'Channel list.' . PHP_EOL . PHP_EOL;
                    $menu = $this->getChannelsMenu('subscribe');
                    break;
                case '/leave':
                    $outputMessage = 'Select channel to leave.' . PHP_EOL . PHP_EOL;
                    $menu = $this->getChannelsMenu('unsubscribe', $chatId);
                    break;
                case '/ping':
                    $outputMessage = 'pong';
                    break;
                case (bool)preg_match(
                    '!/add-channel (\w+)(?:( \d{10,}:\w+)|)!',
                    $command,
                    $m
                ): // do not remove "(bool)"!
                    $this->channel->add($m[1], $m[2] ?? $this->token);
                    $outputMessage = 'Added channel: ' . $m[1];
                    break;
                case (bool)preg_match(
                    '!/update-channel (\w+)(?:( \d{10,}:\w+)|)!',
                    $command,
                    $m
                ): // do not remove "(bool)"!
                    $this->channel->up($m[1], $m[2] ?? $this->token);
                    $outputMessage = 'Updated channel: ' . $m[1];
                    break;
                case (bool)preg_match('|/drop-channel (\w+)|', $command, $m): // do not remove "(bool)"!
                    $this->dropChannel($m[1]);
                    $outputMessage = 'Dropped channel ' . $m[1];
                    break;
                case (bool)preg_match('|/set-token (\d+:\w+)|', $command, $m): // do not remove "(bool)"!
                    $this->setToken($m[1]);
                    $outputMessage = 'New token: ' . $m[1];
                    break;
                case '/show-token':
                    $outputMessage = 'Token is: ' . $this->token;
                    break;
                case '/help':
                    $outputMessage = $this->getHelp();
                    break;
                case '/debug':
                    $outputMessage = json_encode($inputMessage, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
                    break;
                default:
                    $outputMessage = 'Unknown command: ' . $command;
            }
        }

        if ($outputMessage) {
            $token = $this->getTokenByChatId($chatId);
            $telegram = new Tg($token ?: $this->token);
            $telegram->send($chatId, $outputMessage, $menu);
        }
    }

    private function getChannelsMenu(string $targetOperation, int $chatId = null): array
    {
        $buttons = [];
        $channels = $this->channel->getList($chatId);
        foreach ($channels as $channel) {
            $callbackData = json_encode(
                [
                    'action' => $targetOperation,
                    'channelId' => $channel->id,
                ],
                JSON_THROW_ON_ERROR
            );
            $buttons[] = [
                'text' => ucfirst($targetOperation) . ' channel ' . ucfirst($channel->name),
                'callback_data' => $callbackData
            ];
        }

        return ['inline_keyboard' => [$buttons]];
    }

    private function dropChannel(string $channel): void
    {
        $ch = $this->channel->getByName($channel);

        $chats = $this->chats->getChats($ch->id);

        $telegram = new Tg($ch->token);
        foreach ($chats as $chat) {
            $telegram->send($chat->chatId, 'Channel "' . $ch->name . '" has been deleted.');
            $chat->delete();
        }

        $ch->delete();
    }

    private function setToken(string $token): void
    {
        $this->token = $token;
        $this->dynamicConfig->set($this->configScope, 'token', $this->token);
    }

    private function getHelp(): string
    {
        return '/help - quick reference' . PHP_EOL
            . '/menu - show menu' . PHP_EOL
            . '/ping - health check, answer "pong"' . PHP_EOL
            . '/set-token token' . PHP_EOL
            . '/show-token' . PHP_EOL
            . '/add-channel channel-name  [token] - add a new one channel' . PHP_EOL
            . '/update-channel channel-name [token] - update exists channel by name' . PHP_EOL
            . '/drop-channel channel-name - drop an exists channel and all linked chats' . PHP_EOL
            . '/list or /channels - list of all channels and menu to subscribe' . PHP_EOL
            . '/leave - list of all subscribed channels and menu to leave channel' . PHP_EOL
            . '/debug - show debug raw message (json)';
    }
}