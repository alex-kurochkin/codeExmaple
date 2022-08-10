<?php

declare(strict_types=1);

namespace api\models\message\ars;

use api\models\Ar;
use api\models\message\Chat;

class ChatAr extends Ar
{
    public static array $map = [
        'id' => 'id',
        'channelId' => 'channelId',
        'chatId' => 'chatId',
    ];

    public static function tableName(): string
    {
        return '{{%MessageChat}}';
    }

    public static function getModelName(): string
    {
        return Chat::class;
    }

    public static function findChats(int $channelId): array
    {
        return self::find()->where(['channelId' => $channelId])->all();
    }

    public static function findByChatId(int $chatId): ?self
    {
        return self::find()->where(['chatId' => $chatId])->one();
    }

    public static function findByChannelIdAndChatId(int $channelId, int $chatId): ?self
    {
        return self::find()->where(['channelId' => $channelId, 'chatId' => $chatId])->one();
    }
}