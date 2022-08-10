<?php

declare(strict_types=1);

namespace api\controllers\params\user;

use api\controllers\params\Params;

class LoginParams extends Params
{
    public const SCENARIO_LOGIN_SIMPLE = 1;
    public const SCENARIO_LOGIN_2FA = 2;

    public string $email = '';
    public string $password = '';
    public string $code2fa = '';

    public function rules(): array
    {
        return [
            [['email', 'password'], 'required', 'on' => self::SCENARIO_LOGIN_SIMPLE],
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'userExists'],
            ['password', 'string'],
            ['password', 'checkPassword'],
            ['code2fa', 'required', 'on' => self::SCENARIO_LOGIN_2FA],
            ['code2fa', 'string', 'min' => 32],
        ];
    }
}