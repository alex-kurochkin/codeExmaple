<?php

declare(strict_types=1);

namespace api\services\notify\api;

use api\models\Config;
use api\models\logs\MailLog;
use api\services\notify\Notificator;
use RuntimeException;
use Sendinblue\Mailin;

class MailinblueNotificator extends Notificator
{
    protected const NOTIFY_CLASSNAME = MailinblueMail::class;
    private Mailin $mailApi;

    public function __construct()
    {
        $this->mailApi = new Mailin(Config::getSendinServerUrl(), Config::getSendinApiKey());
    }

    public function send(MailNotify $mailinblueMail): bool
    {
        $result = $this->mailApi->send_email($mailinblueMail->getMailData());
        if (is_array($result) && 'failure' === $result['code'] && isset($result['message'])) {
            MailLog::error('Sendinblue error: ' . $result['message'], $result);
            throw new RuntimeException('Sendinblue error: ' . $result['message']);
        }

        MailLog::info('Sendinblue response: ' . $result['message'], $result);

        return true;
    }
}