<?php

declare(strict_types=1);

namespace api\controllers\params\message;

use yii\helpers\ArrayHelper;

class PrivateRentMessageParams extends MessageParams
{
    public ?int $userId = null;
    public $cityId;
    public $countryId;
    public string $messenger = '';
    public string $username = '';
    public string $message = '';
    public int $devicesCount = 1;

    public function rules(): array
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['countryId', 'cityId', 'messenger', 'username', 'devicesCount'], 'required'],
                [['countryId', 'cityId', 'devicesCount'], 'integer', 'min' => 1],
                [['messenger', 'username', 'message'], 'string'],
                ['userId', 'integer'],
                ['userId', 'userExists'],
                ['countryId', 'countryExists'],
                ['cityId', 'cityExists'],
            ]
        );
    }
}