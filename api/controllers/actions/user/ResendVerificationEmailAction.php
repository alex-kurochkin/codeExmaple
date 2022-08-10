<?php

declare(strict_types=1);

namespace api\controllers\actions\user;

use api\controllers\actions\Action;
use api\controllers\params\user\ResendVerificationEmailParams;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\services\UserService;

class ResendVerificationEmailAction extends Action
{
    private UserService $userService;

    public function __construct($id, $controller, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $controller, $config);
    }

    public function run(): array
    {
        $params = new ResendVerificationEmailParams();

        $params->load($this->request->post(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        $this->userService->resendVerificationEmail($params->email);

        return [true];
    }
}