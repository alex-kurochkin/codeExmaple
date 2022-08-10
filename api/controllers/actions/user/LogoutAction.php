<?php

declare(strict_types=1);

namespace api\controllers\actions\user;

use api\controllers\actions\Action;
use api\models\exception\ForbiddenException;
use api\models\logs\LoginLog;
use api\models\user\User;

class LogoutAction extends Action
{
    public function run(): array
    {
        if (!$userId = User::getCurrentUserId()) {
            throw new ForbiddenException('You are not allowed to perform logout.');
        }

        LoginLog::info('Logout user', ['userId' => $userId]);

        $user = User::get($userId);
        $user->logout();
        $user->update();

        return [true];
    }
}