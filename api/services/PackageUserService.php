<?php

declare(strict_types=1);

namespace api\services;

use api\models\Config;
use api\models\exception\NotFoundException;
use api\models\package\api\RsocksAdminRelocatePackageApi;
use api\models\package\api\RsocksChangeIpApi;
use api\models\package\api\RsocksChangeLoginPasswordApi;
use api\models\package\api\RsocksChangeProxyTypeApi;
use api\models\package\api\RsocksMpPauseApi;
use api\models\package\api\RsocksProlongApi;
use api\models\package\api\RsocksStartApi;
use api\models\package\api\RsocksTariffListApi;
use api\models\package\Device;
use api\models\package\KnownCountry;
use api\models\package\Operator;
use api\models\package\Package;
use api\models\package\PackageOperationLog;
use api\models\package\Tariff;
use api\models\user\User;
use api\models\user\UserBalanceLog;
use LogicException;
use stdClass;

class PackageUserService extends PackageService
{
    public static function autoProlong(
        RsocksProlongApi $prolongApi,
        Package $package
    ): void {
        $packages = $package->getNearExpired();
        foreach ($packages as $pkg) {
            $user = User::get($pkg->userId);

            if ($user->balance < $pkg->cost) {
                continue;
            }

            $apiResult = $prolongApi->prolongPackage($pkg->id, $pkg->duration);
            if (!$apiResult->result) {
                self::handleRsocksApiError($apiResult);
            }

            $pkg->prolong($pkg->duration);
            $pkg->update();

            (new PackageOperationLog())->add(
                $pkg,
                PackageOperationLog::OPERATION_PROLONG,
                0 // cron
            );

            $user = User::get($pkg->userId)->decreaseBalance($pkg->cost);

            (new UserBalanceLog())->add(
                $pkg->userId,
                null,
                UserBalanceLog::OPERATION_AUTO_PROLONG,
                UserBalanceLog::RESULT_OK,
                $user->getBalance(),
                -$pkg->cost,
                $pkg->id,
                $pkg->deviceId
            );
        }
    }

    /**
     * @param RsocksTariffListApi $tariffListApi
     * @return stdClass
     * Get fresh tariff list from rsocks api.
     */
    public function getSyncTariffsList(RsocksTariffListApi $tariffListApi): stdClass
    {
        return $tariffListApi->getTariffs();
    }

    public function getTariffsList(): array
    {
        return (new Device())->getAllWithTariffs();
    }

    // $orderBy, $orderDirection

    public function getList(Package $package, int $userId, int $proxyStart, int $proxyCount): array
    {
        User::hasAccess($userId);
        $this->recountTimeLeft($userId);

        return $package->getList($userId, $proxyStart, $proxyCount);
    }

    public function recountTimeLeft(int $userId = null): void
    {
        $updatedIds = Package::recountTimeLeft($userId);
        PackageOperationLog::addDonePackages($updatedIds, 0);
    }

    public function getInProcessingList(Package $package, int $userId, int $start = null, int $count = null): array
    {
        User::hasAccess($userId);
        $this->recountTimeLeft($userId);

        return $package->getInProcessingList($userId, $start, $count);
    }

    public function getHistoryListByStatus(
        Package $package,
        int $userId,
        string $status,
        int $proxyStart,
        int $proxyCount
    ): array {
        $this->recountTimeLeft($userId);

        if ('all' === $status) {
            $statuses = Package::getStatuses();
        } else {
            $statuses = Package::getStatusNumber($status);
        }

        return $package->getHistoryListByStatuses($userId, $statuses, $proxyStart, $proxyCount);
    }

    public function getCountByUserId(Package $package, int $userId): int
    {
        return $package->countByUserId($userId);
    }

    public function getUserActiveCount(Package $package, int $userId): int
    {
        return $package->countInProcessingByUserId($userId);
    }

    public function getCountByUserIdAndStatus(Package $package, int $userId, string $status): int
    {
        if ('all' === $status) {
            $statuses = Package::getStatuses();
        } else {
            $statuses = Package::getStatusNumber($status);
        }

        return $package->countByUserIdAndStatuses($userId, $statuses);
    }

    public function getActiveCountriesListWithDevicesCount(?string $orderDirection): array
    {
        return (new KnownCountry())->getActiveWithDevicesCount($orderDirection);
    }

    public function getPackage(Package $package, int $packageId): Package
    {
        $currentPackage = $package->getById($packageId);
        User::hasAccess($currentPackage->userId);

        $currentPackage->updateTimeLeft();
        $currentPackage->update();

        $currentPackage->tariffs = (new Tariff())->getPackageTariffs($currentPackage);

        $currentPackage->device = (new Device())->getById($currentPackage->deviceId);

        return $currentPackage;
    }

    /**
     * @param int $cityId
     * @param int $operatorId
     * @return Device[]
     * @throws NotFoundException
     */
    public function getActiveDevicesWithTariffs(int $cityId, int $operatorId): array
    {
        $devices = (new Device)->getActiveListByCityIdAndOperatorId($cityId, $operatorId);

        foreach ($devices as &$device) {
            $device->tariffs = (new Tariff())->getByDeviceId($device->id);
        }

        return $devices;
    }

    public function changeIp(
        RsocksChangeIpApi $changeIpApi,
        Package $package,
        int $actorId,
        int $packageId,
        string $fromIp,
        string $toIp
    ): Package {
        $currentPackage = $package->getById($packageId);

        User::hasAccess($currentPackage->userId);

        if (Package::TYPE_IP !== $currentPackage->type) {
            throw new LogicException('Method not allowed');
        }

        $apiResult = $changeIpApi->changeIp($packageId, $fromIp, $toIp);
        if (!$apiResult->result) {
            self::handleRsocksApiError($apiResult);
        }

        $currentPackage->changeUserIp($toIp);

        (new PackageOperationLog())->add(
            $currentPackage,
            PackageOperationLog::OPERATION_CHANGE_USER_IP,
            $actorId
        );

        return $currentPackage;
    }

    public function changeLoginPassword(
        RsocksChangeLoginPasswordApi $changeLoginPasswordApi,
        Package $package,
        int $actorId,
        int $packageId,
        string $login,
        string $password
    ): Package {
        $currentPackage = $package->getById($packageId);

        User::hasAccess($currentPackage->userId);

        if (Package::TYPE_USER !== $currentPackage->type) {
            throw new LogicException('Method not allowed');
        }

        $apiResult = $changeLoginPasswordApi->changeLoginPassword($packageId, $login, $password);
        if (!$apiResult->result) {
            self::handleRsocksApiError($apiResult);
        }

        $currentPackage->changeUserCredentials($login, $password);

        (new PackageOperationLog())->add(
            $currentPackage,
            PackageOperationLog::OPERATION_CHANGE_USER_CREDENTIALS,
            $actorId
        );

        return $currentPackage;
    }

    public function changeType(
        RsocksChangeProxyTypeApi $changeTypeApi,
        Package $package,
        int $actorId,
        int $packageId,
        string $type,
        string $ip,
        string $login,
        string $password
    ): Package {
        $currentPackage = $package->getById($packageId);

        User::hasAccess($currentPackage->userId);

        $apiResult = $changeTypeApi->changeType($packageId, $type, $ip, $login, $password);
        if (!$apiResult->result) {
            self::handleRsocksApiError($apiResult);
        }

        $currentPackage->changeType(Package::getTypeNumber($type), $ip, $login, $password);

        (new PackageOperationLog())->add(
            $currentPackage,
            'user' === $type ? PackageOperationLog::OPERATION_SET_TYPE_USER : PackageOperationLog::OPERATION_SET_TYPE_IP,
            $actorId
        );

        return $currentPackage;
    }

    public function pause(
        RsocksMpPauseApi $pauseApi,
        Package $package,
        int $actorId,
        int $userId,
        int $packageId
    ): Package {
        $currentPackage = $package->getById($packageId);

        User::hasAccess($currentPackage->userId);

        if ($currentPackage->isDone()) {
            throw new LogicException('Can not pause: this package status is DONE.');
        }

        if ($currentPackage->isOnPause()) {
            throw new LogicException('Package always paused');
        }

        $user = User::get($userId);
        $pausePrice = Config::getPackagePausePrice();

        if ($pausePrice > $user->balance) {
            throw new LogicException('Insufficient funds in the account');
        }

        $apiResult = $pauseApi->pausePackage($packageId, $pausePrice);
        if (!$apiResult->result) {
            self::handleRsocksApiError($apiResult);
        }

        $currentPackage->pause();
        $user = User::get($userId)->decreaseBalance($pausePrice);

        (new UserBalanceLog())->add(
            $userId,
            $actorId,
            UserBalanceLog::OPERATION_USER_PAUSE_PACKAGE,
            UserBalanceLog::RESULT_OK,
            $user->getBalance(),
            -$pausePrice,
            $currentPackage->id,
            $currentPackage->deviceId
        );

        (new PackageOperationLog())->add(
            $currentPackage,
            PackageOperationLog::OPERATION_SET_STATUS_PAUSE,
            $actorId
        );

        return $currentPackage;
    }

    public function start(
        RsocksStartApi $startApi,
        Package $package,
        int $actorId,
        int $packageId
    ): Package {
        $currentPackage = $package->getById($packageId);

        User::hasAccess($currentPackage->userId);

        if ($currentPackage->isDone()) {
            throw new LogicException('Can not start this package: status is DONE.');
        }

        if ($currentPackage->isActive()) {
            throw new LogicException('Package always active');
        }

        $apiResult = $startApi->startPackage($packageId);
        if (!$apiResult->result) {
            self::handleRsocksApiError($apiResult);
        }

        $currentPackage->start();

        (new PackageOperationLog())->add(
            $currentPackage,
            PackageOperationLog::OPERATION_SET_STATUS_ACTIVE,
            $actorId
        );

        return $currentPackage;
    }

    /**
     * @param RsocksProlongApi $prolongApi
     * @param Package $package
     * @param int $actorId
     * @param int $packageId Package id
     * @param int $incrementTime seconds
     * @return Package
     */
    public function prolong(
        RsocksProlongApi $prolongApi,
        Package $package,
        int $actorId,
        int $packageId,
        int $incrementTime
    ): Package {
        $currentPackage = $package->getById($packageId);

        User::hasAccess($currentPackage->userId);

        if (!$currentPackage->canProlong()) {
            throw new LogicException('Can not switch auto prolong: this package status is DONE.');
        }

        $user = User::get($currentPackage->userId);

        $tariff = (new Tariff)->getPackageTariff($currentPackage, $incrementTime);

        if ($tariff->cost > $user->balance) {
            throw new LogicException('Insufficient funds in the account');
        }

        $apiResult = $prolongApi->prolongPackage($packageId, $incrementTime);
        if (!$apiResult->result) {
            self::handleRsocksApiError($apiResult);
        }

        $currentPackage->prolong($incrementTime);
        $currentPackage->update();

        (new PackageOperationLog())->add(
            $currentPackage,
            PackageOperationLog::OPERATION_PROLONG,
            $actorId
        );

        $user = User::get($currentPackage->userId)->decreaseBalance($tariff->cost);

        (new UserBalanceLog())->add(
            $currentPackage->userId,
            $actorId,
            UserBalanceLog::OPERATION_USER_PROLONG,
            UserBalanceLog::RESULT_OK,
            $user->getBalance(),
            -$tariff->cost,
            $currentPackage->id,
            $currentPackage->deviceId
        );

        return $currentPackage;
    }

    public function switchAutoProlong(
        Package $package,
        int $packageId,
        bool $switch
    ): Package {
        $currentPackage = $package->getById($packageId);

        User::hasAccess($currentPackage->userId);

        if ($currentPackage->isDone()) {
            throw new LogicException('Can not switch auto prolong: this package status is DONE.');
        }

        $currentPackage->switchAutoProlong($switch);

        return $currentPackage;
    }

    public function searchOperator(
        ?int $countryId = null,
        ?int $cityId = null,
        ?string $orderDirection = null,
        bool $onlyActive = false
    ): array {
        return (new Operator())->search($countryId, $cityId, $orderDirection, $onlyActive);
    }

    public function relocate(
        RsocksAdminRelocatePackageApi $relocatePackageApi,
        Package $package,
        int $actorId,
        int $packageId,
        int $countryId,
        int $cityId,
        int $operatorId,
        int $deviceId
    ): Package {
        $currentPackage = $package->getById($packageId);

        User::hasAccess($currentPackage->userId);

        if (!$currentPackage->userCanRelocate) {
            throw new LogicException('This package cannot be relocated by user');
        }

        if (
            $currentPackage->relocatedAt &&
            time() < $currentPackage->relocatedAt + $currentPackage->relocateDelay * 60
        ) {
            throw new LogicException('This package cannot be relocated: delay restriction');
        }

        return parent::relocate(
            $relocatePackageApi,
            $currentPackage,
            $actorId,
            $packageId,
            $countryId,
            $cityId,
            $operatorId,
            0
        );
    }
}