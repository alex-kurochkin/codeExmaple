<?php

declare(strict_types=1);

namespace api\controllers\params\user;

use api\controllers\params\Params;

class ResetAllSessionsParams extends Params
{
    public $userId;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['userId', 'required'],
            ['userId', 'integer'],
            ['userId', 'userExists'],
        ];
    }
}