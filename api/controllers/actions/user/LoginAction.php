<?php

declare(strict_types=1);

namespace api\controllers\actions\user;

use api\controllers\actions\Action;
use api\controllers\params\user\LoginParams;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\models\logs\LoginLog;
use api\models\user\User;
use api\services\UserService;

class LoginAction extends Action
{
    private UserService $userService;

    public function __construct($id, $controller, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $controller, $config);
    }

    public function run(): array
    {
        if ($userId = User::getCurrentUserId()) {
            LoginLog::info('Double login - logout user', ['userId' => $userId]);

            $loggedInUser = User::get($userId);
            $loggedInUser->logout();
            $loggedInUser->update();

            unset($loggedInUser);
        }

        $params = new LoginParams();

        $params->load($this->request->post(), '');

        $params->scenario = '' === $params->code2fa ? $params::SCENARIO_LOGIN_SIMPLE : $params::SCENARIO_LOGIN_2FA;

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        LoginLog::info('Login', ['params' => $params]);

        // Second part of 2fa - search user by 2fa code and send access token
        if ($params->code2fa) {
            $user = $this->userService->login2fa($params->code2fa);
            LoginLog::info(
                'User logged in by 2fa',
                [
                    'userId' => $user->id,
                    'email' => $user->email,
                    'accessToken' => $user->getAccessToken(),
                    'code2fa' => $params->code2fa
                ]
            );

            return ['accessToken' => $user->getAccessToken()];
        }

        // Try to get user by login/pass pair
        $user = $this->userService->login($params->email, $params->password);

        // Here we got user by login/pass and...

        if ($user->use2fa) {
            // User login procedure send`s email with login URL - wait user on second 2fa part
            LoginLog::info(
                'Login wait 2fa',
                [
                    'userId' => $user->id,
                    'email' => $user->email,
                    'accessToken' => $user->getAccessToken(),
                    'code2fa' => $params->code2fa
                ]
            );

            return ['result' => 'wait2fa'];
        }

        // User login by simple scenario
        LoginLog::info(
            'User logged in simple',
            ['userId' => $user->id, 'email' => $user->email, 'accessToken' => $user->getAccessToken()]
        );

        return ['accessToken' => $user->getAccessToken()];
    }
}