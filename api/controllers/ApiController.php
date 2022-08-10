<?php

declare(strict_types=1);

namespace api\controllers;

use api\models\Config;
use api\models\exception\BadRequestException;
use api\models\Process;
use yii\filters\Cors;
use yii\web\Controller;

abstract class ApiController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => [
                    Config::getCorsOrigin(),
                ],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['Content-Type', 'Authorization', 'Version'],
                'Access-Control-Allow-Credentials' => false,
                'Access-Control-Max-Age' => 3600,
                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
            ],
        ];

        return $behaviors;
    }

    public function beforeAction($action): bool
    {
        $contentType = $this->request->headers['Content-Type'];
        if (
            $contentType && preg_match('|application//json|', $contentType)
            && in_array($this->request->method, ['POST', 'PUT', 'PATCH'], true)
        ) {
            throw new BadRequestException('Bad request: wrong Content-Type');
        }

        return parent::beforeAction($action);
    }

    public function __construct($id, $module, $config = [])
    {
        Process::init();
        parent::__construct($id, $module, $config);
    }
}