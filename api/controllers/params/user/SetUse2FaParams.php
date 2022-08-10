<?php

declare(strict_types=1);

namespace api\controllers\params\user;

use api\controllers\params\Params;

class SetUse2FaParams extends Params
{
    public $userId;
    public $use2fa;

    public function rules(): array
    {
        return [
            [['use2fa'], 'required'],
            [['use2fa'], 'boolean'],
            ['userId', 'integer'],
            ['userId', 'userExists'],
        ];
    }
}