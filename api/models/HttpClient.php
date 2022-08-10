<?php

declare(strict_types=1);

namespace api\models;

use GuzzleHttp\Client as GuzzleHttpClient;
use yii\helpers\ArrayHelper;

class HttpClient
{
    private const TIMEOUT = 30;

    private const HTTP_USER_AGENT = 'mp/http';

    private array $defaultOptions = [
        'headers' => [
            'User-Agent' => self::HTTP_USER_AGENT,
            'Accept' => '*/*'
        ],
        'connect_timeout' => self::TIMEOUT,
        'allow_redirects' => false,
    ];

    public function create(array $options = []): GuzzleHttpClient
    {
        $options = ArrayHelper::merge($this->defaultOptions, $options);

        return new GuzzleHttpClient($options);
    }

    public function setHttpAuth(string $username, string $password): void
    {
        $this->defaultOptions['headers']['Authorization'] = ['Basic ' . base64_encode($username . ':' . $password)];
    }
}