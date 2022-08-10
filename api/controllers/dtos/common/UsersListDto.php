<?php

declare(strict_types=1);

namespace api\controllers\dtos\common;

use api\controllers\dtos\MultipleDto;

class UsersListDto extends MultipleDto
{
    protected static string $singularClassName = UserDto::class;
}