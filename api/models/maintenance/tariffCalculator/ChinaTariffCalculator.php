<?php

declare(strict_types=1);

namespace api\models\maintenance\tariffCalculator;

class ChinaTariffCalculator extends TariffCalculator
{
    public function calculate(string $durationName, int $cost): int
    {
        /** The most right algo is:
         * $cost = (int)round(($cost + $cost / 10) * 100);
         * but RS gets only dollars without cents.
         */
//        if (5 <= $cost) {
//            $cost += (int)ceil($cost / 10);
//        }

        if ('hour' === $durationName) {
            return 3;
        }

        return $cost;
    }
}