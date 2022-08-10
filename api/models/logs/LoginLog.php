<?php

declare(strict_types=1);

namespace api\models\logs;

use api\models\Log;
use api\models\Security;

class LoginLog extends Log
{
    protected const CATEGORY = 'login';

    public static function info($message, array $info = null): void
    {
        $info['auth'] = Security::getHttpAuthorization();
        parent::info($message, $info);
    }
}