<?php

declare(strict_types=1);

namespace api\models\exception;

use api\models\Process;
use yii\web\BadRequestHttpException;

class ApiParamsBadRequestHttpException extends BadRequestHttpException implements ApiParamsExceptionInterface
{
    public string $field;
    public string $hash;

    public function __construct(/*Params*/ $dto)
    {
        parent::__construct($dto->getErrorString(), 0, null);
        $this->field = $dto->getField();
        $this->hash = Process::getPid();
    }

    /**
     * @return mixed
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     */
    public function setField(string $field): void
    {
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function getHash(): string
    {
        return $this->hash;
    }
}