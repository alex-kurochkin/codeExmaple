<?php

declare(strict_types=1);

namespace api\controllers\actions\message;

use api\controllers\actions\Action;
use api\controllers\params\message\MessageParams;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\models\logs\MessengerLog;
use api\models\message\TelegramMessage;
use Exception;
use RuntimeException;

class SendAction extends Action
{
    private TelegramMessage $message;

    public function __construct($id, $controller, TelegramMessage $message, $config = [])
    {
        $this->message = $message;
        parent::__construct($id, $controller, $config);
    }

    public function run(): bool
    {
        try {
            $post = $this->request->post();

            if (array_key_exists('channel', $post)) {
                $this->processUserMessage($post);
                return true;
            }

            $this->message->processChat();
            return true;
        } catch (Exception $e) {
            MessengerLog::error($e);
            throw $e;
        }
    }

    private function processUserMessage(array $post): void
    {
        if (!array_key_exists('format', $post)) {
            throw new RuntimeException(__METHOD__ . ': field "format" not found in the user message');
        }

        $params = MessageParams::getParamsInstance($post['format']);
        $params->load($post, '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        $this->message->processUserMessage($params);
    }
}