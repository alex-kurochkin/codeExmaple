<?php

declare(strict_types=1);

namespace api\controllers\actions\user;

use api\controllers\actions\Action;
use api\controllers\params\user\PasswordResetRequestParams;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\models\logs\LoginLog;
use api\services\UserService;

class RequestPasswordResetAction extends Action
{
    private UserService $userService;

    public function __construct($id, $controller, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $controller, $config);
    }

    public function run(): array
    {
        $params = new PasswordResetRequestParams();
        $params->load($this->request->post(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        LoginLog::info('Reset password request', ['params' => $params]);

        $this->userService->resetPasswordResetToken($params->email);

        return [true];
    }
}