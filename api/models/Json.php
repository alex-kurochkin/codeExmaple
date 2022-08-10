<?php

declare(strict_types=1);

namespace api\models;

use InvalidArgumentException;
use JsonException;

class Json
{
    public static function decode($json, $asArray = false)
    {
        if (is_array($json)) {
            throw new InvalidArgumentException('Invalid JSON data.');
        }

        if (null === $json || '' === $json) {
            return null;
        }

        try {
            $decode = json_decode((string)$json, $asArray, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidArgumentException('JSON decode error: ' . $e->getMessage(), $e->getCode());
        }

        return $decode;
    }
}