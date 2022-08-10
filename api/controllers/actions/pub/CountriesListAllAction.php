<?php

declare(strict_types=1);

namespace api\controllers\actions\pub;

use api\controllers\actions\Action;
use api\models\common\Country;

class CountriesListAllAction extends Action
{
    public function run(): array
    {
        return (new Country)->getAll();
    }
}