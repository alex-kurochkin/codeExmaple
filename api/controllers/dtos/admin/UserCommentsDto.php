<?php

declare(strict_types=1);

namespace api\controllers\dtos\admin;

use api\controllers\dtos\MultipleDto;

class UserCommentsDto extends MultipleDto
{
    protected static string $singularClassName = UserCommentDto::class;
}