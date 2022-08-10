<?php

declare(strict_types=1);

namespace api\models\message;

use stdClass;

abstract class Message
{
    abstract protected function process(stdClass $inputMessage): void;
}