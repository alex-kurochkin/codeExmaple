<?php

declare(strict_types=1);

namespace api\models\exception;

use api\models\ErrorCode;

class BadRequestException extends HttpException
{
    protected $code = ErrorCode::BAD_REQUEST;
    protected int $statusCode = ErrorCode::BAD_REQUEST;
}