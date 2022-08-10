<?php

declare(strict_types=1);

namespace api\controllers\dtos;

use api\models\Model;

/**
 * @property-read string $errorString
 */
abstract class Dto
{
    protected static string $singularClassName = '';

    public function manyToResponse(array $models): array
    {
        $dtos = [];
        foreach ($models as $k => $model) {
            if (static::$singularClassName) {
                $dto = new static::$singularClassName($model);

                $dtos[$k] = $dto;
//                if ($dto instanceof SingleDto) {
//                }

                continue;
            }

            $dto = clone($this);
            $dtos[$k] = $dto->oneToResponse($model);
        }

        return $dtos;
    }

    public function oneToResponse(Model $model): self
    {
        $dto = clone $this;
        foreach ($this as $k => $v) {
            if (isset($model->$k)) {
                $this->$k = $model->$k;
                $dto->$k = $model->$k;
            }
        }

        return $dto;
    }
}