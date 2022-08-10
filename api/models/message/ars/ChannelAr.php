<?php

declare(strict_types=1);

namespace api\models\message\ars;

use api\models\Ar;
use api\models\message\Channel;

class ChannelAr extends Ar
{
    public static array $map = [
        'id' => 'id',
        'name' => 'name',
        'token' => 'token',
    ];

    public static function getModelName(): string
    {
        return Channel::class;
    }

    public static function findByName(string $channel)
    {
        return self::find()->where(['name' => $channel])->one();
    }

    public static function list(int $chatId = null): array
    {
        $find = self::find();

        if ($chatId) {
            $find
                ->join('JOIN', ChatAr::tableName(), ChatAr::tableName() . '.channelId = ' . self::tableName() . '.id')
                ->where(['chatId' => $chatId]);
        }

        return $find->all();
    }

    public static function tableName(): string
    {
        return '{{%MessageChannel}}';
    }
}