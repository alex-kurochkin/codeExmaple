<?php

declare(strict_types=1);

namespace api\controllers\params;

use yii\base\Model as ParamsModel;

/**
 * @property-read string $errorString
 */
abstract class Params extends ParamsModel
{
    use ParamsCustomValidator;

    private string $field = '';

    public function getErrorString(): string
    {
        if (!$allErrors = $this->getErrors()) {
            return '';
        }

        $this->field = array_keys($allErrors)[0];
        return array_shift($allErrors)[0];
    }

    public function getField(): string
    {
        return $this->field;
    }
}