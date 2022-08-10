<?php

use api\components\MailUrlManager;
use api\models\ErrorHandler;
use api\models\user\User;
use yii\log\FileTarget;
use yii\web\Request;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'name' => 'mp',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [
    ],
    'components' => [
        'db' => [
            'charset' => 'utf8',
            'on afterOpen' => static function ($event) {
                $event->sender->createCommand('SET time_zone=\'+00:00\'')->execute();
            },
        ],
        'mailUrlManager' => [
            'class' => MailUrlManager::class,
        ],
        'request' => [
            'class' => Request::class,
            'enableCsrfValidation' => false,
            'enableCsrfCookie' => false,
            'parsers' => [
                'application/json' => yii\web\JsonParser::class,
            ],
        ],
        'errorHandler' => [
            'class' => ErrorHandler::class,
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'on beforeSend' => static function ($event) {
                $response = &$event->sender;
                ErrorHandler::format($response);
            },
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'maxFileSize' => 2048,
                    'exportInterval' => 1, // def: 1000
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['api'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/api.log',
                    'maxFileSize' => 2048,
                    'exportInterval' => 1,
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['payment'],
                    'logFile' => '@runtime/logs/payment.log',
                    'logVars' => [],
                    'maxFileSize' => 2048,
                    'exportInterval' => 1,
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['package'],
                    'logFile' => '@runtime/logs/package.log',
//                    'logVars' => [],
                    'maxFileSize' => 2048,
                    'exportInterval' => 1,
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['sync'],
                    'logFile' => '@runtime/logs/sync.log',
                    'logVars' => [],
                    'maxFileSize' => 2048,
                    'exportInterval' => 1,
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['mail'],
                    'logFile' => '@runtime/logs/mail.log',
                    'logVars' => [],
                    'maxFileSize' => 2048,
//                    'exportInterval' => 1,
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['login'],
                    'logFile' => '@runtime/logs/login.log',
                    'logVars' => [],
                    'maxFileSize' => 2048,
                    'exportInterval' => 1,
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['messenger'],
                    'logFile' => '@runtime/logs/messenger.log',
                    'logVars' => [],
                    'maxFileSize' => 2048,
                    'exportInterval' => 1,
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['sms'],
                    'logFile' => '@runtime/logs/sms.log',
                    'logVars' => [],
                    'maxFileSize' => 2048,
                    'exportInterval' => 1,
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => FileTarget::class,
                    'categories' => ['set-role'],
                    'logFile' => '@runtime/logs/set-role.log',
                    'logVars' => [],
                    'maxFileSize' => 2048,
                    'exportInterval' => 1,
                    'maxLogFiles' => 10,
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            /*'enableStrictParsing' => false,
            'rules' => [
                'a/<id:\d+>' => 'a/index',
            ],*/
        ],
    ],
    'params' => $params,
];
