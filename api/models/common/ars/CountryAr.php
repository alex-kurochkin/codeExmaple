<?php

declare(strict_types=1);

namespace api\models\common\ars;

use api\models\Ar;
use api\models\common\Country;

/**
 * @property int $id [int(11)]
 * @property string $iso [char(2)]
 * @property string $en [varchar(96)]
 * @property string $ru [varchar(96)]
 */
class CountryAr extends Ar
{
    protected static array $map = [
        'id' => 'id',
        'iso' => 'iso',
        'en' => 'en',
        'ru' => 'ru',
    ];

    public static function tableName(): string
    {
        return '{{%country}}';
    }

    public static function getModelName(): string
    {
        return Country::class;
    }

    public static function findAllWithDevices()
    {
        return self::find()->with(['devices'])->all();
    }

    public static function findByName(string $name)
    {
        return self::find()->where(['en' => $name])->one();
    }
}