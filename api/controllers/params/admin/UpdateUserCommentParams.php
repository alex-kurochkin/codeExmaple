<?php

declare(strict_types=1);

namespace api\controllers\params\admin;

use api\controllers\params\Params;

class UpdateUserCommentParams extends Params
{
    public $commentId;
    public $comment;

    public function rules(): array
    {
        return [
            [['commentId', 'comment'], 'required'],
            ['commentId', 'commentExists'],
            ['comment', 'string', 'min' => 1, 'max' => 500],
        ];
    }
}