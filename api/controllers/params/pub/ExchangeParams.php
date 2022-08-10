<?php

declare(strict_types=1);

namespace api\controllers\params\pub;

use api\controllers\params\Params;

class ExchangeParams extends Params
{
    public string $currency = 'cny';

    public function rules(): array
    {
        return [
//            ['currency', 'required'],
            ['currency', 'string'],
            ['currency', 'in', 'range' => ['cny', 'rub', 'btc']],
        ];
    }
}