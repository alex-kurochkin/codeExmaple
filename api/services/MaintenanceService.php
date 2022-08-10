<?php

declare(strict_types=1);

namespace api\services;

use api\models\Container;
use api\models\logs\SyncLog;
use api\models\maintenance\SyncTariffs;
use api\models\package\Device;
use api\models\package\Package;
use api\models\package\PackageOperationLog;
use RuntimeException;
use Yii;

class MaintenanceService
{
    private SyncTariffs $syncTariffs;

    public function __construct(SyncTariffs $syncTariffs)
    {
        $this->syncTariffs = $syncTariffs;
    }

    public function syncTariffs(PackageUserService $packageService): int
    {
        SyncLog::info('Start to syncing tariffs');

        $rsDevices = $this->getRsocksDevices($packageService);

        if (!$transaction = Yii::$app->db->beginTransaction()) {
            throw new RuntimeException('Can not start transaction');
        }

        $errorsCount = $this->syncTariffs->sync($rsDevices);

        $this->cancelPackagesWithDoneDevices();

        $transaction->commit();

        SyncLog::info('Syncing tariffs done');

        return $errorsCount;
    }

    private function getRsocksDevices(PackageUserService $packageService): array
    {
        $apiResponse = Container::invoke([$packageService, 'getSyncTariffsList']);

        if (!$apiResponse || !$apiResponse->result) {
            throw new RuntimeException('Error found on rsocks file/show-mobile-device call');
        }

        return $apiResponse->devices;
    }

    private function cancelPackagesWithDoneDevices(): void
    {
        $deviceDeletedIds = Device::getDeletedIds();

        $deletedPackages = Package::cancelPackagesByDeletedDevices($deviceDeletedIds);

        PackageOperationLog::addDeletedPackages($deletedPackages, 0);
    }
}