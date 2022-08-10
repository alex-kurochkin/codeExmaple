<?php

declare(strict_types=1);

namespace api\controllers\params\admin;

use api\controllers\params\Params;

class GetUserCommentParams extends Params
{
    public $userId;

    public function rules(): array
    {
        return [
            ['userId', 'required'],
            ['userId', 'integer', 'min' => 1],
            ['userId', 'userExists'],
        ];
    }
}