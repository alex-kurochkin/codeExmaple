<?php

declare(strict_types=1);

namespace api\controllers\params\user;

use api\controllers\params\Params;
use api\models\user\User;

class PasswordResetRequestParams extends Params
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
            ['email', 'userExists'],
            [
                'email',
                function ($attribute) {
                    if ($this->errorString) {
                        return;
                    }

                    $user = User::getByEmail($this->email);

                    if (User::STATUS_ACTIVE !== $user->status) {
                        $this->addError($attribute, 'User is not active.');
                    }
                }
            ],
        ];
    }
}