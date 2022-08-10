<?php

namespace api\vendorReplace\Nbobtc\Http;

use api\vendorReplace\Nbobtc\Http\Driver\CurlDriver;
use Nbobtc\Command\CommandInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;

class Client extends \Nbobtc\Http\Client
{
    protected $driver;

    public function __construct($dsn)
    {
        $this->driver = new CurlDriver();
        $this->request = (new Request($dsn))->withHeader('Content-Type', 'application/json');
    }

    public function sendCommand(CommandInterface $command)
    {
        $body = new Stream('php://temp', 'w+');
        $body->write(
            json_encode(
                array(
                    'method' => $command->getMethod(),
                    'params' => $command->getParameters(),
                    'id' => $command->getId(),
                ),
                JSON_THROW_ON_ERROR
            )
        );

        $request = $this->request->withBody($body);

        /** @var ResponseInterface */
        $this->response = $this->driver->execute($request);

        return $this->response;
    }
}