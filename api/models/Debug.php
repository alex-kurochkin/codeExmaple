<?php

declare(strict_types=1);

namespace api\models;

use Yii;

class Debug
{
    public static function log($data, string $name = ''): void
    {
        static $appendType = 0;

        $flags = $appendType ? FILE_APPEND : 0;

        if (!$appendType) {
            $appendType = 1;
        }

        $message = '[' . date('c') . '] ';

        if ($name) {
            $message .= '' . $name . ': ';
        }

        $fn = Yii::getAlias('@apiRuntime/logs/debug.log');

        if (is_scalar($data)) {
            $message .= $data;
        } else {
            $message .= var_export($data, true);
        }

        file_put_contents($fn, $message . PHP_EOL, $flags);
    }
}