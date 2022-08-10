<?php

declare(strict_types=1);

namespace api\models\common;

interface UserOwnedInterface
{
    public function getUserId(): int;
}