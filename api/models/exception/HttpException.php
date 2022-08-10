<?php

declare(strict_types=1);

namespace api\models\exception;

use api\models\ErrorCode;
use Exception;

class HttpException extends Exception
{
    protected $code = ErrorCode::OK;
    protected int $statusCode = ErrorCode::OK;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}