<?php

declare(strict_types=1);

namespace api\controllers;

use api\controllers\actions\message\SendAction;
use yii\filters\VerbFilter;

class MessageController extends ApiController
{
    public function actions(): array
    {
        return [
            'send' => SendAction::class,
        ];
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors += [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'send' => ['POST', 'PUT', 'OPTIONS'],
                ],
            ]
        ];

        return $behaviors;
    }
}