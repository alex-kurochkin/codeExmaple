<?php

declare(strict_types=1);

namespace api\models\message;

use api\models\message\ars\ChannelAr;
use api\models\Model;

class Channel extends Model
{
    public int $id;
    public string $name;
    public string $token;

    /**
     * @param int|null $chatId
     * @return self[]
     */
    public function getList(int $chatId = null): array
    {
        return $this->importMany(ChannelAr::list($chatId));
    }

    public function add(string $channelName, string $token): void
    {
        $channel = new self();
        $channel->name = $channelName;
        $channel->token = $token ?? $this->token;
        $channel->save();
    }

    public function up(string $channelName, string $token): self
    {
        $ch = $this->getByName($channelName);
        $ch->name = $channelName;
        $ch->token = $token;
        return $ch->update();
    }

    public function getByName(string $channelName): Channel
    {
        return $this->importOne(ChannelAr::findByName($channelName));
    }

    public function delete(): void
    {
        ChannelAr::deleteAll(['name' => $this->name]);
    }
}