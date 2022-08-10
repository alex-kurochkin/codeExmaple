<?php

declare(strict_types=1);

namespace api\controllers\actions\admin;

use api\controllers\actions\Action;
use api\controllers\dtos\common\UserDto;
use api\controllers\params\admin\UserInfoParams;
use api\models\Container;
use api\models\exception\ApiParamsBadRequestHttpException;
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
        $params = new UserInfoParams();
        $params->load($this->request->get(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        $user = Container::invoke(
            [$this->userService, 'getUserByIdProxiesList'],
            ['userId' => $params->userId, 'proxyStart' => $params->proxyStart, 'proxyCount' => $params->proxyCount]
        );

        $userDto = new UserDto($user);

        $userDto->proxiesCountAll = Container::invoke(
            [$this->userService, 'getUserProxiesCount'],
            ['userId' => $params->userId]
        );

        return $userDto;
    }
}