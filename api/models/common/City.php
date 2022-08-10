<?php

declare(strict_types=1);

namespace api\models\common;

use api\models\common\ars\CityAr;
use api\models\Model;

class City extends Model
{
    public int $id;
    public int $countryId;
    public int $important;
    public string $ru;
    public ?string $areaRu;
    public ?string $regionRu;
    public string $en;
    public ?string $areaEn;
    public ?string $regionEn;

    public function getCitiesListByCountryId(int $countryId, ?string $search, ?string $language): array
    {
        return $this->importMany(CityAr::getCitiesListByCountryId($countryId, $search, $language));
    }
}