<?php

declare(strict_types=1);

namespace api\controllers\dtos;

use api\models\Model;
use IteratorAggregate;
use yii\base\Arrayable;

abstract class MultipleDto extends Dto implements IteratorAggregate, Arrayable, \Countable
{
    private array $storage = [];

    /**
     * MultipleDto constructor.
     * @param Model[] $models
     */
    public function __construct(array $models = [])
    {
        if ($models) {
            $this->storage = $this->manyToResponse($models);
        }
    }

    public function count(): int
    {
        return count($this->storage);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->storage);
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return $this->storage;
    }

    public function fields(): array
    {
        return [];
    }

    public function extraFields(): array
    {
        return [];
    }
}