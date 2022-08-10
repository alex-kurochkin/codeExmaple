<?php

declare(strict_types=1);

namespace api\controllers\params\admin;

use api\controllers\params\Params;

class DeleteUserCommentParams extends Params
{
    public $commentId;

    public function rules(): array
    {
        return [
            ['commentId', 'required'],
            ['commentId', 'commentExists'],
        ];
    }
}