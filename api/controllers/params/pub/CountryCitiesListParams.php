<?php

declare(strict_types=1);

namespace api\controllers\params\pub;

use api\controllers\params\Params;

class CountryCitiesListParams extends Params
{
    public $countryId;
    public string $search = '';
    public string $language = '';

    public function rules(): array
    {
        return [
            ['countryId', 'required'],
            ['countryId', 'integer', 'min' => 1],
            ['countryId', 'countryExists'],
            ['search', 'string'],
            [
                'language',
                'required',
                'when' => static function ($params) {
                    return (bool)$params->search;
                }
            ],
            ['language', 'in', 'range' => ['en', 'ru']],
        ];
    }
}