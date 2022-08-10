<?php

declare(strict_types=1);

namespace api\controllers\params\user;

use api\controllers\params\Params;

class FastLoginParams extends Params
{
    public string $email = '';
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
            ['language', 'in', 'range' => ['en', 'ru', 'ch']],
        ];
    }
}