<?php

declare(strict_types=1);

namespace api\models;

use Yii;

class Log
{
    protected const CATEGORY = '';

    public static function info($message, array $info = null): void
    {
        Yii::info(self::formatMessage($message, $info), static::CATEGORY);
        Yii::getLogger()->flush();
    }

    private static function formatMessage($message, array $info = null): array
    {
        if ($info) {
            array_walk_recursive(
                $info,
                static function (&$item, $key) {
                    if (is_object($item)) {
                        $item = get_object_vars($item);
                    }
                }
            );
        }

        return [
            'pid' => $pid = Process::getPid(),
            'message' => $message,
            'info' => $info,
        ];
    }

    public static function error($message, array $info = null): void
    {
        Yii::error(self::formatMessage($message, $info), static::CATEGORY);
        Yii::getLogger()->flush();
    }
}