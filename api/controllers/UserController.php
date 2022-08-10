<?php

declare(strict_types=1);

namespace api\controllers;

use api\controllers\actions\user\LoginAction;
use api\controllers\actions\user\LogoutAction;
use api\controllers\actions\user\RequestPasswordResetAction;
use api\controllers\actions\user\ResendVerificationEmailAction;
use api\controllers\actions\user\ResetAllSessionsAction;
use api\controllers\actions\user\ResetPasswordAction;
use api\controllers\actions\user\SendVerificationPhoneCodeAction;
use api\controllers\actions\user\SignupAction;
use api\controllers\actions\user\UserInfoAction;
use api\controllers\actions\user\VerifyEmailAction;
use api\controllers\actions\user\VerifyPhoneAction;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;

class UserController extends ApiController
{
    private bool $useBearerToken = false;

    public function actions(): array
    {
        return [
            'info' => UserInfoAction::class,
            'verify-phone' => VerifyPhoneAction::class,
            'send-verification-phone-code' => SendVerificationPhoneCodeAction::class,
            'login' => LoginAction::class,
            'logout' => LogoutAction::class,
            'signup' => SignupAction::class,
            'resend-verification-email' => ResendVerificationEmailAction::class,
            'request-password-reset' => RequestPasswordResetAction::class,
            'reset-password' => ResetPasswordAction::class,
            'verify-email' => VerifyEmailAction::class,
            'reset-all-sessions' => ResetAllSessionsAction::class,
        ];
    }

    public function beforeAction($action): bool
    {
        if (in_array(
            $action->id,
            [
                'verify-phone',
                'send-verification-phone-code',
                'info',
                'logout',
                'set-language',
                'set-use2fa',
                'reset-all-sessions',
            ],
            true
        )) {
            $this->useBearerToken = true;
        }

        return parent::beforeAction($action);
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        if ($this->useBearerToken) {
            $behaviors += [
                'authenticator' => [
                    'class' => HttpBearerAuth::class,
                ],
            ];
        }

        $behaviors += [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'info' => ['GET', 'OPTIONS'],
                    'verify-phone' => ['PATCH', 'OPTIONS'],
                    'send-verification-phone-code' => ['POST', 'OPTIONS'],
                    'signup' => ['PUT', 'OPTIONS'],
                    'login' => ['POST', 'OPTIONS'],
                    'logout' => ['POST', 'OPTIONS'],
                    'request-password-reset' => ['POST', 'OPTIONS'],
                    'reset-password' => ['PATCH', 'OPTIONS'],
                    'verify-email' => ['POST', 'OPTIONS'],
                    'resend-verification-email' => ['GET', 'OPTIONS'],
                    'reset-all-sessions' => ['POST', 'OPTIONS'],
                ],
            ]
        ];

        return $behaviors;
    }
}