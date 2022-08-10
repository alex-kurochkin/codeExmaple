<?php

declare(strict_types=1);

namespace api\models\dynamicConfig;

use api\models\dynamicConfig\ars\DynamicConfigAr;
use api\models\Transaction;
use RuntimeException;
use yii\db\Exception;

class DynamicConfig
{
    /** @var DynamicConfigAr */
    private DynamicConfigAr $ar;

    public function __construct()
    {
        $this->ar = new DynamicConfigAr();
    }

    /**
     * @param string $scope
     * @param string $key
     * @param int|float|string $value
     * @param int|null $expireAt Unix timestamp - point in the future
     * @return $this
     * @throws Exception
     */
    public function set(string $scope, string $key, $value, int $expireAt = null): self
    {
        if (!$transaction = Transaction::begin()) {
            throw new RuntimeException(__CLASS__ . ' can not get transaction');
        }

        $this->delete($scope, $key);

        $ar = clone($this->ar);
        $ar->set($scope, $key, $value, $expireAt);
        $ar->save();

        $transaction->commit();

        return $this;
    }

    public function delete(string $scope, string $key): void
    {
        if ($ars = $this->ar->get($scope, $key)) {
            foreach ($ars as $ar) {
                $ar->delete();
            }
        }
    }

    public function get(string $scope, string $key): ?string
    {
        if (1 !== count($config = $this->ar->get($scope, $key))) {
            $this->clearExpired($config);
        }

        if (0 === count($config)) {
            return null;
        }

        $param = $config[0];

        if ($param->expire && $param->expire <= time()) {
            $this->ar->deleteExpired();
            return null;
        }

        return $param->value;
    }

    private function clearExpired(array $params): array
    {
        $needClear = false;

        foreach ($params as $k => $param) {
            if ($param->expire && $param->expire <= time()) {
                $needClear = true;
                unset($params[$k]);
            }
        }

        if ($needClear) {
            $this->ar->deleteExpired();
        }

        return $params;
    }

    public function search(string $scope, string $search): array
    {
        $list = $this->ar->search($scope, $search);
        foreach ($list as &$item) {
            $item = (object)$item;
        }

        return $list;
    }

    public function searchByValue(string $scope, $value): array
    {
        $list = $this->ar->searchByValue($scope, $value);
        foreach ($list as &$item) {
            $item = (object)$item;
        }

        return $list;
    }
}