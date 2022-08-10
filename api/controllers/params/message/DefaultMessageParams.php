<?php

declare(strict_types=1);

namespace api\controllers\params\message;

use ArrayIterator;

class DefaultMessageParams extends MessageParams
{
    private array $values;

    public function load($data, $formName = null): bool
    {
        $this->values = $data;
        return parent::load($data, $formName);
    }

    public function rules(): array
    {
        return [];
    }

    public function __get($name)
    {
        return $this->values[$name];
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }
}