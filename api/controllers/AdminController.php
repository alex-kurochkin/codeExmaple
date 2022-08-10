<?php

declare(strict_types=1);

namespace api\controllers;

use api\controllers\actions\admin\DeleteUserCommentAction;
use api\controllers\actions\admin\GetUserCommentsAction;
use api\controllers\actions\admin\AddUserCommentAction;
use api\controllers\actions\admin\UpdateUserCommentAction;
use api\controllers\actions\admin\UserInfoAction;
use api\controllers\actions\admin\UsersListAction;
use api\models\security\HttpBearerAdminAuth;
use yii\filters\VerbFilter;

class AdminController extends ApiController
{
    public function actions(): array
    {
        return [
            'user-info' => UserInfoAction::class,
            'users-list' => UsersListAction::class,
            'add-user-comment' => AddUserCommentAction::class,
            'update-user-comment' => UpdateUserCommentAction::class,
            'delete-user-comment' => DeleteUserCommentAction::class,
            'get-user-comments' => GetUserCommentsAction::class,
        ];
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors += [
            'authenticator' => [
                'class' => HttpBearerAdminAuth::class,
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'user-info' => ['GET', 'OPTIONS'],
                    'users-list' => ['GET', 'OPTIONS'],
                    'add-user-comment' => ['PUT', 'OPTIONS'],
                    'update-user-comment' => ['POST', 'OPTIONS'],
                    'delete-user-comment' => ['DELETE', 'OPTIONS'],
                    'get-user-comments' => ['GET', 'OPTIONS'],
                ],
            ]
        ];

        return $behaviors;
    }
}