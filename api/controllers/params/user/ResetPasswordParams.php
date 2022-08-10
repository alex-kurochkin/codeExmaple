<?php

declare(strict_types=1);

namespace api\controllers\params\user;

use api\controllers\params\Params;
use api\models\Config;

class ResetPasswordParams extends Params
{
    public string $token = '';
    public string $password = '';

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['password', 'token'], 'required'],
            ['password', 'string', 'min' => Config::getUserPasswordMinLength()],
            ['token', 'string', 'min' => 43],
            ['token', 'checkPasswordResetToken'],
        ];
    }
}