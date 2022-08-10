<?php

declare(strict_types=1);

namespace api\controllers\params\user;

use api\controllers\params\Params;

class ResendVerificationEmailParams extends Params
{
    public string $email = '';
    public string $language = '';

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['email', 'required'],
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'userIsInactive'],
            ['language', 'in', 'range' => ['en', 'ru', 'ch']],
        ];
    }
}