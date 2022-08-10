<?php

declare(strict_types=1);

namespace api\models\exception;

use api\models\ErrorCode;

class ForbiddenException extends HttpException
{
    protected $code = ErrorCode::FORBIDDEN;
    protected int $statusCode = ErrorCode::FORBIDDEN;
}