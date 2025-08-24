<?php

namespace BaksDev\Article\Repository\Search\AllArticleToIndex;

interface AllArticleResultInterface
{
    public function getArticleTitle(): string;

    public function getArticleContent(): string;

    public function getArticleId(): string;
}