<?php

declare(strict_types=1);

namespace api\controllers;

use api\controllers\actions\pub\CountriesListAllAction;
use api\controllers\actions\pub\CountryCitiesListAction;
use api\controllers\actions\pub\ExchangeAction;
use yii\filters\VerbFilter;

class PublicController extends ApiController
{
    public function actions(): array
    {
        return [
            'countries-list-all' => CountriesListAllAction::class,
            'country-cities-list' => CountryCitiesListAction::class,
            'exchange' => ExchangeAction::class,
        ];
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors += [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'countries-list-all' => ['GET', 'OPTIONS'],
                    'country-cities-list' => ['GET', 'OPTIONS'],
                    'exchange' => ['GET', 'OPTIONS'],
                ],
            ]
        ];

        return $behaviors;
    }
}