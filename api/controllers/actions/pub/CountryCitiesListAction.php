<?php

declare(strict_types=1);

namespace api\controllers\actions\pub;

use api\controllers\actions\Action;
use api\controllers\dtos\common\CitiesListDto;
use api\controllers\params\pub\CountryCitiesListParams;
use api\models\common\City;
use api\models\exception\ApiParamsBadRequestHttpException;

class CountryCitiesListAction extends Action
{
    public function run(): CitiesListDto
    {
        $params = new CountryCitiesListParams();
        $params->load($this->request->get(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        $citiesList = (new City())->getCitiesListByCountryId(
            (int)$params->countryId,
            $params->search,
            $params->language
        );

        return new CitiesListDto($citiesList);
    }
}