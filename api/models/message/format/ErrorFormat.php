<?php

declare(strict_types=1);

namespace api\models\message\format;

use api\controllers\params\message\MessageParams;

class ErrorFormat extends Format
{
    public function format(MessageParams $message): string
    {
        return 'Server: ' . $message->server . PHP_EOL
            . 'Error' . PHP_EOL
            . $message->error . PHP_EOL;
    }
}