<?php

declare(strict_types=1);

namespace api\models\exception;

interface ApiParamsExceptionInterface
{
    public function __construct(/*Params */ $dto);

    public function getField(): string;

    public function setField(string $field): void;

    public function getHash(): string;
}