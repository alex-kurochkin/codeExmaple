<?php

declare(strict_types=1);

namespace api\models\message\format;

use api\controllers\params\message\MessageParams;

class DefaultFormat extends Format
{
    public function format(MessageParams $message): string
    {
        $str = '';
        foreach ($message as $k => $v) {
            $str .= $k . ': ' . $v . PHP_EOL;
        }

        return $str;
    }
}