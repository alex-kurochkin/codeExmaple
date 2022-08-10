<?php

declare(strict_types=1);

namespace api\controllers\actions\admin;

use api\controllers\actions\Action;
use api\controllers\dtos\common\UsersListDto;
use api\controllers\params\admin\UsersListParams;
use api\models\Container;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\services\UserService;

class UsersListAction extends Action
{
    private UserService $userService;

    public function __construct($id, $controller, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $controller, $config);
    }

    public function run(): array
    {
        $params = new UsersListParams();
        $params->load($this->request->get(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        $list = Container::invoke(
            [$this->userService, 'getList'],
            [
                'role' => $params->role,
                'searchUserString' => $params->searchUser,
                'start' => $params->start,
                'count' => $params->count,
                'orderBy' => $params->orderBy,
                'orderDirection' => $params->orderDirection,
            ]
        );

        return [
            'users' => (new UsersListDto($list['users'])),
            'count' => count($list['users']),
            'countAll' => $list['countAll'],
        ];
    }
}