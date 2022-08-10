<?php

declare(strict_types=1);

namespace api\controllers\params\user;

use api\controllers\params\Params;

class PhoneParams extends Params
{
    public $phone;

    public function rules(): array
    {
        return [
            ['phone', 'required'],
            [
                'phone',
                'filter',
                'filter' => [$this, 'phoneFilter']
            ],
            ['phone', 'number', 'message' => 'Phone must be phone number.'],
        ];
    }
}