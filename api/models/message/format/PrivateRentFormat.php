<?php

declare(strict_types=1);

namespace api\models\message\format;

use api\controllers\params\message\MessageParams;
use api\models\common\City;
use api\models\common\Country;
use api\models\user\User;

class PrivateRentFormat extends Format
{
    public function format(MessageParams $message): string
    {
        $user = '<Guest>';
        if ($message->userId) {
            $user = User::get($message->userId)->email . ' [' . $message->userId . ']';
        }

        $country = Country::get($message->countryId);
        $city = City::get($message->cityId);

        return 'Server: ' . $message->server . PHP_EOL
            . 'User: ' . $user . PHP_EOL
            . 'Страна: ' . $country->en . PHP_EOL
            . 'Город: ' . $city->en . PHP_EOL
            . 'Messenger: ' . $message->messenger . PHP_EOL
            . 'Кол-во девайсов: ' . $message->devicesCount . PHP_EOL
            . 'Username: ' . $message->username;
    }
}