<?php

declare(strict_types=1);

namespace api\models\logs;

use api\models\Log;

class MailLog extends Log
{
    protected const CATEGORY = 'mail';
}