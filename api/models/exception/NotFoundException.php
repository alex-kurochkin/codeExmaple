<?php

declare(strict_types=1);

namespace api\models\exception;

use api\models\ErrorCode;

class NotFoundException extends HttpException
{
    protected $code = ErrorCode::NOT_FOUND;
    protected int $statusCode = ErrorCode::NOT_FOUND;
}