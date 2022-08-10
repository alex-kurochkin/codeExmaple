<?php

declare(strict_types=1);

namespace api\controllers\params\user;

use api\controllers\params\Params;
use api\models\Config;

class SignupParams extends Params
{
    public string $email = '';
    public string $password = '';
    public string $language = '';

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['email', 'language'], 'required'],
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'emailIsFree'],
            ['password', 'required'],
            ['password', 'string', 'min' => Config::getUserPasswordMinLength()],
            ['language', 'in', 'range' => ['en', 'ru', 'ch']],
        ];
    }
}