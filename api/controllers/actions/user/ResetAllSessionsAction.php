<?php

declare(strict_types=1);

namespace api\controllers\actions\user;

use api\controllers\actions\Action;
use api\controllers\params\user\ResetAllSessionsParams;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\models\exception\ForbiddenException;
use api\models\logs\LoginLog;
use api\models\user\User;
use api\services\UserService;

class ResetAllSessionsAction extends Action
{
    private UserService $userService;

    public function __construct($id, $controller, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $controller, $config);
    }

    public function run(): bool
    {
        $params = new ResetAllSessionsParams();

        $params->load($this->request->post(), '');

        $userId = $params->userId ?? User::getCurrentUserId();

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        if (!User::hasAccess($userId)) {
            throw new ForbiddenException('Access denied');
        }

        LoginLog::info('Reset all sessions', ['params' => $params]);

        $this->userService->resetAllSessions($userId);

        return true;
    }
}