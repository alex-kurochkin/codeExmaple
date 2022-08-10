<?php

declare(strict_types=1);

namespace api\controllers\params\user;

use api\controllers\params\Params;

class VerifyEmailParams extends Params
{
    public string $token = '';

    public function rules(): array
    {
        return [
            ['token', 'required'],
            ['token', 'string', 'min' => 43],
            ['token', 'checkVerificationCode'],
        ];
    }
}