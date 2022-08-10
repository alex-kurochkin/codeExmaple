<?php

declare(strict_types=1);

namespace api\controllers\actions\user;

use api\controllers\actions\Action;
use api\controllers\params\user\ResetPasswordParams;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\models\logs\LoginLog;
use api\services\UserService;

class ResetPasswordAction extends Action
{
    private UserService $userService;

    public function __construct($id, $controller, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $controller, $config);
    }

    public function run(): array
    {
        $params = new ResetPasswordParams();

        $params->load($this->request->post(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        LoginLog::info('Reset password', ['params' => $params]);

        $this->userService->resetPassword($params->token, $params->password);

        return [true];
    }
}