<?php

declare(strict_types=1);

namespace api\models;

use api\models\logs\ApiLog;
use GuzzleHttp\Client as GuzzleHttpClient;
use RuntimeException;
use stdClass;
use Throwable;
use yii\helpers\ArrayHelper;

abstract class Api
{
    protected const METHOD_GET = 'GET';
    protected const METHOD_POST = 'POST';
    protected const METHOD_PUT = 'PUT';
    protected const METHOD_PATCH = 'PATCH';
    protected const METHOD_DELETE = 'DELETE';
    protected const METHOD_OPTIONS = 'OPTIONS';
    protected const METHOD_HEAD = 'HEAD';

    protected const METHOD = self::METHOD_GET;

    /** @var GuzzleHttpClient */
    protected GuzzleHttpClient $httpClient;

    protected array $options = [];

    public function __construct(HttpClient $client)
    {
        $this->httpClient = $client->create();
        $this->setOptions();
    }

    abstract protected function setOptions(): void;

    /**
     * @param string $method
     * @param string $urlPath
     * @param array $payload request payload:
     *
     * @return stdClass
     */
    public function request(string $method, string $urlPath, array $payload = []): stdClass
    {
        try {
            $payload = ArrayHelper::merge($this->options, $payload);

            ApiLog::info(
                __METHOD__,
                [
                    'urlPath' => $urlPath,
                    'payload' => $payload,
                ]
            );

            $response = $this->httpClient->request($method, $urlPath, $payload);
            $json = $response->getBody()->read($response->getBody()->getSize());

            ApiLog::info(
                __METHOD__,
                [
                    'urlPath' => $urlPath,
                    'payload' => $payload,
                    'response' => $json,
                ]
            );

            // @todo Should we analyse response code?
            // What if it's 20* but not 200?
//            $code = $response->getStatusCode();

            $apiResponse = Json::decode($json);

            ApiLog::info(
                __METHOD__,
                [
                    'urlPath' => $urlPath,
                    'apiResponse' => $apiResponse,
                ]
            );

            return $apiResponse;
        } catch (Throwable $e) {
            ApiLog::error(
                __METHOD__,
                [
                    'urlPath' => $urlPath,
                    'payload' => $payload,
                    'error' => $e->getMessage(),
                ]
            );

            throw new RuntimeException('Caught exception: ' . $e->getMessage());
        }
    }
}