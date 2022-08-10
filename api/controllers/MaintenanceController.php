<?php

declare(strict_types=1);

namespace api\controllers;

use api\models\Container;
use api\models\ErrorCode;
use api\models\security\HttpBearerAdminAuth;
use api\services\MaintenanceService;
use yii\filters\VerbFilter;

class MaintenanceController extends ApiController
{
    private MaintenanceService $maintenanceService;

    public function __construct($id, $module, MaintenanceService $maintenanceService, $config = [])
    {
        $this->maintenanceService = $maintenanceService;
        parent::__construct($id, $module, $config);
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
                    'sync-tariffs' => ['POST', 'OPTIONS'],
                ],
            ]
        ];

        return $behaviors;
    }

    /**
     * Webhook. Called from rsocks. Start sync devices and tariffs.
     * Call GET https://rsocks-domain/api/v1/file/show-mobile-device
     * @return string
     */
    public function actionSyncTariffs(): string
    {
        $errorsCount = Container::invoke([$this->maintenanceService, 'syncTariffs']);
        $this->response->statusCode = ErrorCode::ACCEPTED;

        return 'Errors count: ' . $errorsCount;
    }
}