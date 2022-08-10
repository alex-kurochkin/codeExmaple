<?php

declare(strict_types=1);

namespace api\models\common;

use api\models\common\ars\CountryAr;
use api\models\Model;
use RuntimeException;

class Country extends Model
{
    public int $id;
    public string $iso;
    public string $en;
    public string $ru;

    public static function getByName(string $countryName): self
    {
        static $countries;

        $countries ??= (new self())->getAll('en');

        if (!array_key_exists($countryName, $countries)) {
            throw new RuntimeException('No country found for name: ' . $countryName);
        }

        return $countries[$countryName];
    }

    public function getAll(string $indexName = null): array
    {
        return $this->importMany(CountryAr::find()->all(), $indexName);
    }
}