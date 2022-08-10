<?php

namespace api\vendorReplace\Nbobtc\Http\Driver;

use Exception;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Response;

class CurlDriver extends \Nbobtc\Http\Driver\CurlDriver
{
    public function execute(RequestInterface $request)
    {
        $uri = $request->getUri();

        if (null === self::$ch || gettype(self::$ch) !== 'curl') {
            self::$ch = curl_init();
        }

        curl_setopt_array(self::$ch, $this->getDefaultCurlOptions());

        curl_setopt(
            self::$ch,
            CURLOPT_URL,
            sprintf('%s://%s@%s%s', $uri->getScheme(), $uri->getUserInfo(), $uri->getHost(), $uri->getPath())
        );
        curl_setopt(self::$ch, CURLOPT_PORT, $uri->getPort());

        $headers = array();
        foreach ($request->getHeaders() as $header => $values) {
            $headers[] = $header . ': ' . implode(', ', $values);
        }
        curl_setopt(self::$ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt(self::$ch, CURLOPT_POSTFIELDS, (string)$request->getBody());

        // Allows user to override any option, may cause errors
        curl_setopt_array(self::$ch, $this->curlOptions);

        /** @var string|false */
        $result = curl_exec(self::$ch);
        /** @var array|false */
        $info = curl_getinfo(self::$ch);
        /** @var string */
        $error = curl_error(self::$ch);

        if (!empty($error)) {
            throw new Exception($error);
        }

        $response = new Response();

        $response = $response->withStatus($info['http_code']);

        $response->getBody()->write($result);
        $response->getBody()->rewind(); // empty getContents() without rewind()

        return $response;
    }
}