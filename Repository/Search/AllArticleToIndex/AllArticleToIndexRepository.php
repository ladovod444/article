<?php

declare(strict_types=1);

namespace BaksDev\Article\Repository\Search\AllArticleToIndex;

use BaksDev\Article\Entity\Article;
use BaksDev\Core\Doctrine\DBALQueryBuilder;



final class AllArticleToIndexRepository implements AllArticleToIndexInterface
{
    private bool $properties = false;

    private int|false $maxResult = false;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
    ) {}

//    public function findAllAllArticleToIndexRepository(): array|bool
//    {
//        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);
//
//        //$dbal->select('id');
//        //$dbal->from(ClasssName::class, 'aliace');
//
//        return $dbal
//            // ->enableCache('Namespace', 3600)
//            ->fetchAllAssociative();
//    }

    /** Максимальное количество записей в результате */
    public function maxResult(int $max): self
    {
        $this->maxResult = $max;
        return $this;
    }

    /** @return array<int, AllArticleResult>|false */
    public function toArray(): array|false
    {
        $result = $this->findAll();

        return (true === $result->valid()) ? iterator_to_array($result) : false;
    }

    public function findAll(): \Generator|false
    {
        // TODO: Implement toArray() method.
        $dbal = $this->builder();

        $dbal->enableCache('article', 86400);

        $result = $dbal->fetchAllHydrate(AllArticleResult::class);

        return (true === $result->valid()) ? $result : false;
    }

    private function builder(): DBALQueryBuilder
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal
            ->select('article.id as article_id')
            ->addSelect('article.title as article_title')
            ->addSelect('article.content as article_content')
            ->from(Article::class, 'article');
        if(false !== $this->maxResult)
        {
            $dbal->setMaxResults($this->maxResult);
        }

        return $dbal;
    }
}