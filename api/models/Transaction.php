<?php

declare(strict_types=1);

namespace api\models;

/**
 * Class Transaction
 * Use this class inside services or Model
 * @package api\models
 */
class Transaction
{
    public static function begin(): \yii\db\Transaction
    {
        return Ar::beginTransaction();
    }
}