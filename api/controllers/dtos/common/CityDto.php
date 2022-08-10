<?php

declare(strict_types=1);

namespace api\controllers\dtos\common;

use api\controllers\dtos\SingleDto;
use api\models\common\City;
use api\models\Model;

class CityDto extends SingleDto
{
    public $id;
    public $countryId;
    public $en;

    /**
     * @param City $model
     * @return self
     */
    public function oneToResponse(Model $model): self
    {
        /** @var CityDto $this */
        parent::oneToResponse($model);

        $this->en = $model->en . ', ' . $model->areaEn;

        return $this;
    }
}