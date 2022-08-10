<?php

declare(strict_types=1);

namespace api\models\logs;

use api\models\Log;

class ApiLog extends Log
{
    protected const CATEGORY = 'api';
}