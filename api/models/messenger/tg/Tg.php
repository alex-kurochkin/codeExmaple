<?php

declare(strict_types=1);

namespace api\models\messenger\tg;

use api\models\Json;
use JsonException;
use LogicException;
use RuntimeException;
use stdClass;

class Tg
{
    private string $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public static function getMessage(): stdClass
    {
        if (!$body = file_get_contents('php://input')) {
            throw new LogicException('Empty message found');
        }

        try {
            return Json::decode($body);
        } catch (JsonException $e) {
            throw new RuntimeException('Broken message found: ' . $e->getMessage());
        }
    }

    public function send($id, $message, $keyboard = null)
    {
        $data = [
            'chat_id' => $id,
            'text' => $message
        ];

        if ($keyboard) {
            if ('DEL' === $keyboard) {
                $keyboard = [
                    'remove_keyboard' => true
                ];
            } else {
                $keyboard['resize_keyboard'] = true;
                $keyboard['one_time_keyboard'] = true;
            }

            $data['reply_markup'] = json_encode($keyboard, JSON_THROW_ON_ERROR);
        }

        return $this->request('sendMessage', $data);
    }

    private function request($method, $data = array())
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/bot' . $this->token . '/' . $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $out = Json::decode(curl_exec($curl));

        curl_close($curl);

        return $out;
    }

    public function getPhoto($data)
    {
        return $this->request('getFile', $data);
    }

    public function savePhoto($url, $path): void
    {
        $ch = curl_init('https://api.telegram.org/file/bot' . $this->token . '/' . $url);
        $fp = fopen($path, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }
}