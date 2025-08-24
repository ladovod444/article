<?php

namespace BaksDev\Article\Repository\Search\AllArticleToIndex;

use BaksDev\Products\Product\Repository\Cards\ModelOrProduct\ModelOrProductResult;

interface AllArticleToIndexInterface
{
    /** Максимальное количество записей в результате */
    public function maxResult(int $max): self;

    /** @return array<int, AllArticleResult>|false */
    public function toArray(): array|false;

    /** @return \Generator<int, AllArticleResult>|false */
    public function findAll(): \Generator|false;
}