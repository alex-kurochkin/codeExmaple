<?php

declare(strict_types=1);

namespace api\controllers\params\admin;

use api\controllers\params\Params;

class SetUserCommentParams extends Params
{
    public $userId;
    public $comment;

    public function rules(): array
    {
        return [
            [['userId', 'comment'], 'required'],
            ['userId', 'userExists'],
            ['comment', 'string', 'min' => 1, 'max' => 500],
        ];
    }
}