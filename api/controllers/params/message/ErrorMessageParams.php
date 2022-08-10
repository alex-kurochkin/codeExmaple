<?php

declare(strict_types=1);

namespace api\controllers\params\message;

class ErrorMessageParams extends MessageParams
{
    public string $error = '';

    public function load($data, $formName = ''): bool
    {
        $this->error = $data['error'];
        return parent::load($data, $formName);
    }
}