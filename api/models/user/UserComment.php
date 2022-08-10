<?php

declare(strict_types=1);

namespace api\models\user;

use api\models\Model;
use api\models\security\AuthManager;
use api\models\user\ars\UserCommentAr;
use LogicException;

class UserComment extends Model
{
    public int $id;
    public int $userId;
    public int $authorId;
    public string $comment;
    public string $datetime;

    public function add(
        int $authorId,
        int $userId,
        string $comment
    ): self {
        $this->authorId = $authorId;
        $this->userId = $userId;
        $this->comment = $comment;
        $this->datetime = date('Y-m-d H:i:s');
        $this->save();

        return $this;
    }

    public function updateComment(int $commentId, int $updaterId, string $comment): void
    {
        $userComment = self::get($commentId);

        if ($userComment->authorId !== $updaterId && !AuthManager::isSu($updaterId)) {
            throw new LogicException('Can\'t update comment, you\'re not the author.');
        }

        $userComment->comment = $comment;
        $userComment->update();
    }

    /**
     * @param int $userId
     * @return self[]
     */
    public function getByUserId(int $userId): array
    {
        return $this->importMany(UserCommentAr::findByUserId($userId));
    }

    public function delete(int $commentId, int $updaterId): void
    {
        $userComment = self::get($commentId);

        if ($userComment->authorId !== $updaterId && !AuthManager::isSu($updaterId)) {
            throw new LogicException('Can\'t update comment, you\'re not the author.');
        }

        $userComment->ar->delete();
    }
}