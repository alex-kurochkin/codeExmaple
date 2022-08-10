<?php

declare(strict_types=1);

namespace api\controllers\actions\user;

use api\controllers\actions\Action;
use api\controllers\dtos\common\UserDto;
use api\models\Container;
use api\models\user\User;
use api\services\UserService;

class UserInfoAction extends Action
{
    private UserService $userService;

    public function __construct($id, $controller, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $controller, $config);
    }

    public function run(): UserDto
    {
        $user = Container::invoke([$this->userService, 'getUserById'], ['userId' => User::getCurrentUserId()]);

        return new UserDto($user);
    }
}