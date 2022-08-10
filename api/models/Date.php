<?php

declare(strict_types=1);

namespace api\models;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;

class Date
{
    /**
     * @param string|null $date
     * @return DateTimeInterface
     * @throws Exception
     */
    public static function make(?string $date): DateTimeInterface
    {
        if ($date) {
            return new DateTimeImmutable($date);
        }

        return new DateTimeImmutable();
    }
}