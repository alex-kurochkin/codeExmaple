<?php

declare(strict_types=1);

namespace api\models\maintenance\tariffCalculator;

class MainTariffCalculator extends TariffCalculator
{
    public function calculate(string $durationName, int $cost): int
    {
        return $cost;
//        $cost += $cost * .2;

//        return (int)round($cost);
    }
}