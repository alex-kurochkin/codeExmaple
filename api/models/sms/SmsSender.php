<?php

declare(strict_types=1);

namespace api\models\sms;

use api\models\Config;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;

class SmsSender
{
    public static function send(string $numberTo, string $message): MessageInstance
    {
        $sid = Config::getTwilioSid();
        $token = Config::getTwilioToken();

        $client = new Client($sid, $token);
        return $client->messages->create(
            $numberTo,
            [
                'from' => Config::getTwilioNumber(),
                'body' => $message
            ]
        );
    }
}