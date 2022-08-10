<?php

declare(strict_types=1);

namespace api\controllers\params\user;

use api\controllers\params\Params;

class SetLanguageParams extends Params
{
    public string $language = '';

    public function rules(): array
    {
        return [
            [['language'], 'required'],
            [['language'], 'in', 'range' => ['en', 'ru', 'ch']],
        ];
    }
}