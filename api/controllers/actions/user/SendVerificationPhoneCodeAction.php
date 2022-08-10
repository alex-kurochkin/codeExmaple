<?php

declare(strict_types=1);

namespace api\controllers\actions\user;

use api\controllers\actions\Action;
use api\controllers\params\user\PhoneParams;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\models\user\User;
use api\services\UserService;

class SendVerificationPhoneCodeAction extends Action
{
    private UserService $userService;

    public function __construct($id, $controller, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $controller, $config);
    }

    public function run(): array
    {
        $params = new PhoneParams();
        $params->load($this->request->post(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        $this->userService->sendVerificationPhoneCode(User::getCurrentUserId(), $params->phone);

        return [true];
    }
}