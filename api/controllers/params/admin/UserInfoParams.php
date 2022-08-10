<?php

declare(strict_types=1);

namespace api\controllers\params\admin;

use api\controllers\params\Params;

class UserInfoParams extends Params
{
    public $userId;
    public int $proxyStart = 0;
    public int $proxyCount = 100;

    public function rules(): array
    {
        return [
            ['userId', 'required'],
            ['userId', 'integer'],
            ['userId', 'userExists'],
            ['proxyStart', 'integer', 'min' => 0],
            ['proxyCount', 'integer', 'min' => 1],
        ];
    }
}