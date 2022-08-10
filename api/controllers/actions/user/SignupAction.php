<?php

declare(strict_types=1);

namespace api\controllers\actions\user;

use api\controllers\actions\Action;
use api\controllers\params\user\SignupParams;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\models\logs\LoginLog;
use api\services\UserService;

class SignupAction extends Action
{
    private UserService $userService;

    public function __construct($id, $controller, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $controller, $config);
    }

    public function run(): array
    {
        $params = new SignupParams();

        $params->load($this->request->post(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        $this->userService->signup($params->email, $params->password, $params->language);

        LoginLog::info('User signed up', ['email' => $params->email]);

        return [true];
    }
}