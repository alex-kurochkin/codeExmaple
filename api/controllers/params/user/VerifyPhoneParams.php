<?php

declare(strict_types=1);

namespace api\controllers\params\user;

use api\controllers\params\Params;

class VerifyPhoneParams extends Params
{
    public $code;

    public function rules(): array
    {
        return [
            ['code', 'required'],
            ['code', 'number', 'min' => 100000, 'max' => 999999],
            ['code', 'verifyPhoneCode'],
        ];
    }
}