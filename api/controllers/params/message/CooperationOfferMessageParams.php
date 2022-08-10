<?php

declare(strict_types=1);

namespace api\controllers\params\message;

use yii\helpers\ArrayHelper;

class CooperationOfferMessageParams extends MessageParams
{
    public ?int $userId = null;
    public string $messenger = '';
    public string $username = '';
    public string $message = '';

    public function rules(): array
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['messenger', 'username', 'message'], 'required'],
                [['messenger', 'username', 'message'], 'string'],
                ['userId', 'integer'],
                ['userId', 'userExists'],
            ]
        );
    }
}