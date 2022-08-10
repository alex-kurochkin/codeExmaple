<?php

declare(strict_types=1);

namespace api\controllers\actions\user;

use api\controllers\actions\Action;
use api\controllers\dtos\common\UserDto;
use api\controllers\params\user\VerifyPhoneParams;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\models\user\User;
use api\services\UserService;

class VerifyPhoneAction extends Action
{
    private UserService $userService;

    public function __construct($id, $controller, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $controller, $config);
    }

    public function run(): UserDto
    {
        $params = new VerifyPhoneParams();
        $params->load($this->request->post(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        $user = $this->userService->setPhoneVerified(User::getCurrentUserId());

        return new UserDto($user);
    }
}