<?php

declare(strict_types=1);

namespace api\models\message;

use api\models\message\ars\ChatAr;
use api\models\Model;

class Chat extends Model
{
    public int $id;
    public int $channelId;
    public int $chatId;

    /**
     * @param int $channelId
     * @return self[]
     */
    public function getChats(int $channelId): array
    {
        return $this->importMany(ChatAr::findChats($channelId));
    }

    public function getChatByChatId(int $chatId): ?self
    {
        $chat = ChatAr::findByChatId($chatId);
        return $chat ? $this->importOne($chat) : null;
    }

    public function subscribe(int $chatId, int $channelId): void
    {
        $subscribe = new self();
        $subscribe->chatId = $chatId;
        $subscribe->channelId = $channelId;
        $subscribe->save();
    }

    public function unsubscribe(int $chatId, int $channelId): void
    {
        (new self())->getByChannelIdAndChatId($channelId, $chatId)->delete();
    }

    public function delete(): void
    {
        ChatAr::deleteAll(['id' => $this->id]);
    }

    public function getByChannelIdAndChatId(int $channelId, int $chatId): self
    {
        return $this->importOne(ChatAr::findByChannelIdAndChatId($channelId, $chatId));
    }

    public function isChatSubscribedToChannel(int $channelId, int $chatId): bool
    {
        return (bool)ChatAr::findByChannelIdAndChatId($channelId, $chatId);
    }
}