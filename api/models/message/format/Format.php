<?php

declare(strict_types=1);

namespace api\models\message\format;

use api\controllers\params\message\MessageParams;

abstract class Format
{
    public static function getFormat(string $formatName): self
    {
        if (!$formatName) {
            $formatName = 'Default';
        }

        $c = __NAMESPACE__ . '\\' . $formatName . 'Format';

        if (!class_exists($c)) {
            $c = DefaultFormat::class;
        }

        return new $c;
    }

    abstract public function format(MessageParams $message): string;
}