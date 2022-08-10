<?php

declare(strict_types=1);

namespace api\models\maintenance;

use api\models\common\Country;
use api\models\Durations;
use api\models\Lock;
use api\models\logs\SyncLog;
use api\models\maintenance\tariffCalculator\TariffCalculator;
use api\models\package\Device;
use api\models\package\KnownCity;
use api\models\package\KnownCountry;
use api\models\package\Operator;
use api\models\package\Tariff;
use RuntimeException;
use stdClass;

class SyncTariffs
{
    private int $errorsCount = 0;

    private Device $device;
    private Tariff $tariff;
    private KnownCountry $knownCountry;
    private KnownCity $knownCity;
    private Operator $operator;
    private TariffCalculator $tariffCalculator;

    public function __construct(
        Device $device,
        Tariff $tariff,
        KnownCountry $knownCountry,
        KnownCity $knownCity,
        Operator $operator
    ) {
        $this->device = $device;
        $this->tariff = $tariff;
        $this->knownCountry = $knownCountry;
        $this->knownCity = $knownCity;
        $this->operator = $operator;
        $this->tariffCalculator = TariffCalculator::getCalculator();
    }

    public function sync(array $rsDevices): int
    {
        Lock::get('sync');

        $this->tariff->reset();
        $this->device->markDeletedAll();
        $this->operator->markDeletedAll();
        $this->knownCity->markDeletedAll();
        $this->knownCountry->markDeletedAll();

        foreach ($rsDevices as $rsDevice) {
            set_time_limit(10);

            if (!$this->addKnownCountry($rsDevice)) {
                SyncLog::error('Not found country ' . $rsDevice->country);
                $this->errorsCount++;
                continue;
            }

            $this->addKnownCity($rsDevice);

            $this->addOperator($rsDevice);

            $deviceId = $this->addDevice($rsDevice);

            $this->addTariffs($deviceId, $rsDevice->tariffs, Tariff::TYPE_GENERAL);
            $this->addTariffs($deviceId, $rsDevice->multiport_tariff, Tariff::TYPE_USER_RELOCATABLE);
        }

        Lock::release('sync');

        return $this->errorsCount;
    }

    private function addKnownCountry(stdClass $rsDevice): bool
    {
        static $knownCountryIds = [];

        try {
            $countryName = CountrySynonym::getName($rsDevice->country);
            $country = Country::getByName($countryName);
        } catch (RuntimeException $e) {
            SyncLog::error($e->getMessage());

            return false;
        }

        if (array_key_exists($rsDevice->country_id, $knownCountryIds)) {
            return true;
        }

        $knownCountryIds[$rsDevice->country_id] = 1;

        if ($knownCountry = KnownCountry::get($rsDevice->country_id, false)) {
            $knownCountry->deleted = false;
            $knownCountry->update();

            return true;
        }

        $this->knownCountry->add($rsDevice->country_id, $country->iso, $country->en, $country->ru);

        return true;
    }

    private function addKnownCity(stdClass $rsDevice): void
    {
        static $knownCityIds = [];

        if (array_key_exists($rsDevice->city_id, $knownCityIds)) {
            return;
        }

        $knownCityIds[$rsDevice->city_id] = 1;

        if ($knownCity = KnownCity::get($rsDevice->city_id, false)) {
            $knownCity->deleted = false;
            $knownCity->update();

            return;
        }

        $this->knownCity->add($rsDevice->city_id, $rsDevice->country_id, $rsDevice->city);
    }

    private function addOperator(stdClass $rsDevice): void
    {
        if ($operator = $this->operator->searchOne($rsDevice->country_id, $rsDevice->city_id, $rsDevice->operator_id)) {
            $operator->deleted = false;
            $operator->update();

            return;
        }

        $this->operator->add(
            $rsDevice->operator_id,
            $rsDevice->operator,
            $rsDevice->country_id,
            $rsDevice->city_id
        );
    }

    private function addDevice($rsDevice): int
    {
        $deviceId = $rsDevice->{'device id'};

        if ($device = Device::get($deviceId, false)) {
            $device->deleted = false;
            $device->update();

            return $deviceId;
        }

        $this->device->add(
            $deviceId,
            $rsDevice->country_id,
            $rsDevice->city,
            $rsDevice->city_id,
            $rsDevice->operator,
            $rsDevice->operator_id
        );

        return $deviceId;
    }

    private function addTariffs(int $deviceId, stdClass $tariffs, int $type): void
    {
        foreach ($tariffs as $durationName => $cost) {
            if ('pause' === $durationName) {
                continue;
            }

            $cost = $this->tariffCalculator->calculate($durationName, $cost);

            (new Tariff)->add(
                $deviceId,
                $type,
                Durations::getDurationSeconds($durationName),
                $cost * 100
            );
        }
    }
}