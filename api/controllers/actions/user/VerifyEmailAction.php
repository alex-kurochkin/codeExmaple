<?php

declare(strict_types=1);

namespace api\controllers\actions\user;

use api\controllers\actions\Action;
use api\controllers\params\user\VerifyEmailParams;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\services\UserService;

class VerifyEmailAction extends Action
{
    private UserService $userService;

    public function __construct($id, $controller, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $controller, $config);
    }

    public function run(): array
    {
        $params = new VerifyEmailParams();

        $params->load($this->request->post(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        $user = $this->userService->verifyEmail($params->token);

        return ['accessToken' => $user->getAccessToken()];
    }
}