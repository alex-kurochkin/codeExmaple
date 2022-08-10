<?php

declare(strict_types=1);

namespace api\models;

use api\models\exception\ApiParamsBadRequestHttpException;
use api\models\exception\ApiParamsExceptionInterface;
use api\models\exception\HttpException;
use api\models\exception\PaymentException;
use Exception;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use Throwable;
use Yii;
use yii\base\ExitException;
use yii\web\HttpException as YiiHttpException;
use yii\web\Response;

class ErrorHandler extends \yii\web\ErrorHandler
{
    /**
     * Method for api/config/main.php
     * But Yii use it on Debug mode only... IRL it's useless
     * @param $response
     */
    public static function format($response): void
    {
        $data = &$response->data;

        $exception = Yii::$app->errorHandler->exception;

        if (!$exception instanceof Exception) {
            return;
        }

        $data['datetime'] = date('c');
        $data['hash'] = Process::getPid();
        $data['field'] = '';

        if ($exception instanceof ApiParamsExceptionInterface) {
            $data['field'] = $exception->getField();
        }

        unset($data['file'], $data['line'], $data['stack-trace'], $data['previous'], $data['error-info']);
    }

    public function handleException($exception): void
    {
        if ($exception instanceof ExitException) {
            return;
        }

        try {
            Log::error($exception);
//        TelegramErrorLogTarget::send(implode(PHP_EOL, $data));

            $this->clearOutput();

            $this->renderException($exception);

            Yii::getLogger()->flush(true);

            exit(1);
        } catch (Throwable $e) {
            $this->handleFallbackExceptionMessage($e, $exception);
        }
    }

    protected function renderException($exception): void
    {
        $response = Yii::$app->response ?? new Response();

        $this->exception = $exception;

        $response->data = [];
        $response->data['type'] = Reflection::getClassShortName($exception);
        $response->data['code'] = $exception->getCode();
        $response->data['datetime'] = date('c');
        $response->data['hash'] = Process::getPid();
        $response->data['message'] = $exception->getMessage();

        switch (true) {
            case $exception instanceof \yii\db\Exception:
                $response->setStatusCode(ErrorCode::INTERNAL_SERVER_ERROR);
                if ('23000' === $exception->getCode()) {
                    $response->setStatusCode(ErrorCode::CONFLICT);
                }

                $response->data['message'] = 'Database error: ' . implode('; ', $exception->errorInfo);
                break;
            case $exception instanceof ApiParamsBadRequestHttpException:
                $response->data['field'] = $exception->getField();
                parent::renderException($exception);
                break;
            case $exception instanceof LogicException:
            case $exception instanceof PaymentException:
                $response->setStatusCode(ErrorCode::BAD_REQUEST);
                break;
            case $exception instanceof YiiHttpException:
                if (ErrorCode::NOT_FOUND === $exception->statusCode) { // do not show any content to crawlers, worms and etc.
                    http_response_code(ErrorCode::NOT_FOUND);
                    print '404 Not Found';
                    die;
                }

                parent::renderException($exception);
                break;
            case $exception instanceof RuntimeException:
            case $exception instanceof InvalidArgumentException:
            default:
                $response->setStatusCode(ErrorCode::INTERNAL_SERVER_ERROR);
        }

        if ($exception instanceof HttpException) {
            $response->setStatusCode($exception->getStatusCode());
        }

        $response->data['status'] = $response->getStatusCode();

        $response->send();
    }
}