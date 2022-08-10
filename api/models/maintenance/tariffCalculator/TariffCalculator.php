<?php

declare(strict_types=1);

namespace api\models\maintenance\tariffCalculator;

use api\models\Config;

abstract class TariffCalculator
{
    public static function getCalculator(): self
    {
        $environment = Config::getAppName();

        switch ($environment) {
            case 'Mobiledizhi':
                $name = 'China';
                break;
            default:
                $name = 'Main';
        }

        $c = __NAMESPACE__ . '\\' . $name . 'TariffCalculator';
        return new $c;
    }

    abstract public function calculate(string $durationName, int $cost): int;
}