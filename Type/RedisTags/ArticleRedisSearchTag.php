<?php

namespace BaksDev\Article\Type\RedisTags;

use BaksDev\Article\Repository\Search\AllArticle\SearchAllArticlesInterface;
use BaksDev\Article\Repository\Search\AllArticleToIndex\AllArticleResult;
use BaksDev\Article\Repository\Search\AllArticleToIndex\AllArticleToIndexInterface;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Switcher\Switcher;
use BaksDev\Products\Product\Repository\Search\AllProducts\SearchAllProductsInterface;
use BaksDev\Products\Product\Repository\Search\AllProductsToIndex\AllProductsToIndexResult;
use BaksDev\Search\RedisSearchDocuments\RedisEntityDocument;
use BaksDev\Search\Repository\DataToIndex\DataToIndexResultInterface;
use BaksDev\Search\Repository\RedisToIndexResult\RedisToIndexResultInterface;
use BaksDev\Search\SearchDocuments\EntityDocumentInterface;
use BaksDev\Search\SearchDocuments\PrepareDocumentInterface;
use BaksDev\Search\SearchIndex\SearchIndexTagInterface;
use BaksDev\Search\Type\RedisTags\Collection\RedisSearchIndexTagInterface;
//use BaksDev\Search\Type\RedisTags\Collection\RedisSearchRepoTagInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

use Generator;

#[AutoconfigureTag('baks.search-tags')]
//#[AutoconfigureTag('baks.redis-tags')]
//class ArticleRedisSearchTag extends AbstractProductRedisSearchTag implements RedisSearchIndexTagInterface
//class ArticleRedisSearchTag implements RedisSearchIndexTagInterface
class ArticleRedisSearchTag implements SearchIndexTagInterface, PrepareDocumentInterface
{

    public function __construct(
        protected readonly AllArticleToIndexInterface $repository,
        protected readonly SearchAllArticlesInterface $searchRepository,
//        protected readonly SearchAllProductsInterface $searchRepository,
        protected readonly Switcher $switcher,
        protected readonly EntityDocumentInterface $entityDocument
    ) {}

    public const string TAG = 'article';

    public const string INDEX_ID = 'article_id';

    public function getValue(): string
    {
        return self::TAG;
    }

    public static function sort(): int
    {
        return 5;
    }

    public function getRepositoryData(): false|Generator
    {
        return $this->repository->findAll();
    }

    public function prepareDocument(DataToIndexResultInterface $item): EntityDocumentInterface
    {
        /** @var AllProductsToIndexResult  $item */
        $documentId = $item->getProductModificationId();

        //        $entityDocument = $entityDoc::getInstance($documentId);

        $this->entityDocument->setEntityId($documentId);
        //        $entityDocument->setEntityId($documentId);



        //        dd(get_class($entityDocument));
        //        $entityDocument = new RedisEntityDocument($documentId);

        //        $transformed_value = $item->getTransformedValue($this->switcher);
        $textSearch = $item->setTextSearch($this->switcher);

        //        $entityDocument
        $this->entityDocument
            ->setEntityIndex($textSearch)
            ->setSearchTag($this->getModuleName());

        //        dump($this->entityDocument);

        return $this->entityDocument;
    }

    public function prepareDocumentOld(RedisToIndexResultInterface $item): RedisEntityDocument
    {


        /** @var $item AllArticleResult */

        $documentId = $item->getArticleId();
        $entityDocument = new RedisEntityDocument($documentId);

        //        $product_article = mb_strtolower(str_replace('-', ' ', $item['product_article']));
        //        $article_name = mb_strtolower($item['product_name']);
//        $article_name = mb_strtolower($item->getArticleTitle());
//
//        // Добавить "ошибочный" вариант Switcher
//        //        $transl_article = $this->switcher->toRus($product_article);
//        $transl_name = $this->switcher->toRus($article_name);

        //getTransformValue

        $transformed_value = $item->getTransformedValue($this->switcher);

        $entityDocument
//            ->setEntityIndex($article_name.' '.$transl_name)
            ->setEntityIndex($transformed_value)
            ->setSearchTag($this->getValue());

        return $entityDocument;
    }

    public function getRepositorySearchData(SearchDTO $search, int|bool $max_results = false): false|Generator
    {
        $repository = $this->searchRepository
            ->search($search);

        if ($max_results !== false) {
            $repository->maxResult($max_results);
        }
        //            ->setTag($this->getValue())
        return $repository->findAll();
    }

    public function getModuleName(): string
    {
        // TODO: Implement getModuleName() method.
        return self::TAG;
    }

    public function getPrepareClass(): string
    {
        // TODO: Implement getPrepareClass() method.
        return 'Article';





    }
}