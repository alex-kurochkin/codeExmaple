<?php

declare(strict_types=1);

namespace api\controllers\dtos;

use api\models\Model;
use api\models\user\User;
use stdClass;

abstract class SingleDto extends Dto
{
    public function __construct(Model $config)
    {
        $this->oneToResponse($config);
    }

    protected function getUserShortObject(?int $userId): stdClass
    {
        if ($userId) {
            $user = User::get($userId);

            return (object)['id' => $user->id, 'email' => $user->email, 'username' => $user->username];
        }

        return (object)['id' => 0, 'email' => 'SYSTEM', 'username' => 'SYSTEM'];
    }
}