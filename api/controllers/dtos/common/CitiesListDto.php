<?php

declare(strict_types=1);

namespace api\controllers\dtos\common;

use api\controllers\dtos\MultipleDto;

class CitiesListDto extends MultipleDto
{
    protected static string $singularClassName = CityDto::class;
}