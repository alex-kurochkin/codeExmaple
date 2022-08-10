<?php

declare(strict_types=1);

namespace api\models\logs;

use api\models\Log;

class MessengerLog extends Log
{
    protected const CATEGORY = 'messenger';
}