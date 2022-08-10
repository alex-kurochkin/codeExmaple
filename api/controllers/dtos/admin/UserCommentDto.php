<?php

declare(strict_types=1);

namespace api\controllers\dtos\admin;

use api\controllers\dtos\SingleDto;
use api\models\exception\NotFoundException;
use api\models\Model;
use api\models\user\User;
use api\models\user\UserComment;
use stdClass;

class UserCommentDto extends SingleDto
{
    public int $id = 0;
    public stdClass $author;
    public int $userId = 0;
    public string $comment = '';
    public string $datetime = '';

    /**
     * @param UserComment $model
     * @throws NotFoundException
     */
    public function oneToResponse(Model $model): self
    {
        /** @var self $dto */
        parent::oneToResponse($model);
        $author = User::get($model->authorId);
        $this->author = (object)[
            'id' => $author->id,
            'username' => $author->username,
            'email' => $author->email,
        ];

        return $this;
    }
}