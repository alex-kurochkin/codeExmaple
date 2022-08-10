<?php

declare(strict_types=1);

namespace api\models\exception;

use api\models\ErrorCode;

class UnauthorizedException extends HttpException
{
    protected $code = ErrorCode::UNAUTHORIZED;
    protected int $statusCode = ErrorCode::UNAUTHORIZED;
}