<?php

use api\services\notify\api\MailinblueNotificator;

return [
    'adminEmail' => 'admin@example.com',
    'app.name' => 'mp',
    'app.corsOrigin' => '',
    'app.serverId' => 0,

    'app.needVerifyPhone' => true,
    'app.needVerifyEmail' => false,

    'app.userLoginIdleLimit' => 31536000, // 1 year
    'app.user2faIdleLimit' => 86400, // 1 day

    'app.userSentPhoneCodeNoDelayAttempt' => 3,
    'app.userSentPhoneCodeDelay' => 600, // seconds

    'app.defaultCurrency' => '', // country currency

    'package.userRelocateDelay' => 10, // minutes

    'notificator.className' => MailinblueNotificator::class,

    'mailinblue.serverUrl' => '',
    'mailinblue.apiKey' => '',

    'twilio.sid' => '',
    'twilio.token' => '',
    'twilio.number' => '',

    'enot.id' => '',
    'enot.url' => 'https://enot.io/pay/',
    'enot.limits' => ['trc20' => 1000, 'erc20' => 6000, 'et' => 4000], // Enot restrictions in rubles
    'enot.apiKey' => '',
    'enot.secretWord' => '',
    'enot.secretWordResult' => '',
];
