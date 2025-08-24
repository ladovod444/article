<?php

namespace BaksDev\Article\Repository\Search\AllArticleToIndex;

use BaksDev\Core\Services\Switcher\Switcher;
use BaksDev\Search\Repository\RedisToIndexResult\RedisToIndexResultInterface;

final readonly class AllArticleResult implements RedisToIndexResultInterface
{

//    private string $article_id;
//    private string $article_title;
//    private string $article_content;

    public function __construct(
//        protected Switcher $switcher,
        private string $article_id,
        private string $article_title,
        private string $article_content,
    ){}

    public function getArticleTitle(): string
    {
        return $this->article_title;
    }

    public function getArticleContent(): string
    {
        return $this->article_content;
    }

    public function getArticleId(): string
    {
        return $this->article_id;
    }

    public function getTransformedValue(Switcher $switcher): string
    {
        // TODO: Implement getTransformValue() method.
        $article_name = mb_strtolower($this->getArticleTitle());


        $switcher = new Switcher();

        // Добавить "ошибочный" вариант Switcher
        //        $transl_article = $this->switcher->toRus($product_article);
        $transl_name = $switcher->toRus($article_name);

        return $article_name.' '.$transl_name;
    }
}