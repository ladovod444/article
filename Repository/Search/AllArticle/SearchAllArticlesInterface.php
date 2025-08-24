<?php

namespace BaksDev\Article\Repository\Search\AllArticle;

use BaksDev\Core\Form\Search\SearchDTO;
use Generator;

interface SearchAllArticlesInterface
{
    public function search(SearchDTO $search): self;

    public function findAll(): Generator|false;

    public function maxResult(int $max): self;
}