<?php

declare(strict_types=1);

namespace api\models\common\ars;

use api\models\Ar;
use api\models\common\City;

/**
 * Class CityAr
 * @package api\models\common\ars
 * @property int $id [int]
 * @property int $countryId [int]
 * @property bool $important [tinyint(1)]
 * @property string $ru [varchar(150)]
 * @property string $areaRu [varchar(150)]
 * @property string $regionRu [varchar(150)]
 * @property string $en [varchar(150)]
 * @property string $areaEn [varchar(150)]
 * @property string $regionEn [varchar(150)]
 */
class CityAr extends Ar
{
    public static array $map = [
        'id' => 'id',
        'countryId' => 'countryId',
        'important' => 'important',
        'ru' => 'ru',
        'areaRu' => 'areaRu',
        'regionRu' => 'regionRu',
        'en' => 'en',
        'areaEn' => 'areaEn',
        'regionEn' => 'regionEn',
    ];

    public static function tableName(): string
    {
        return '{{%city}}';
    }

    public static function getModelName(): string
    {
        return City::class;
    }

    public static function getCitiesListByCountryId(int $countryId, ?string $search, ?string $language): array
    {
        $activeQuery = self::find()
            ->where(['countryId' => $countryId]);

        if ($search) {
            $activeQuery->andWhere(['like', $language, $search . '%', false]);
        }

        return $activeQuery->limit(1000)->orderBy('en')->all();
    }
}