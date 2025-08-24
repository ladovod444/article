<?php

namespace BaksDev\Article\Repository\Search\AllArticle;

use BaksDev\Article\Entity\Article;
use BaksDev\Article\Repository\Search\AllArticleToIndex\AllArticleResult;
use BaksDev\Article\Type\RedisTags\ArticleRedisSearchTag;
use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Search\Index\RedisSearchIndexHandler;
use BaksDev\Users\Profile\UserProfile\Repository\UserProfileTokenStorage\UserProfileTokenStorageInterface;
use Doctrine\DBAL\ArrayParameterType;
use Generator;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;

class SearchAllArticlesRepository implements SearchAllArticlesInterface
{

    const int MAX_RESULTS = 10;

    private ?SearchDTO $search = null;

    private int|false $maxResult = false;

    public function __construct(
        #[Target('articleLogger')] private LoggerInterface $logger,
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly UserProfileTokenStorageInterface $userProfileTokenStorage,
        private readonly ?RedisSearchIndexHandler $redisSearchIndexHandler = null,
    ) {}

    /** Максимальное количество записей в результате */
    public function maxResult(int $max): self
    {
        $this->maxResult = $max;
        return $this;
    }

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }

    public function findAll(): Generator|false
    {
        if(is_null($this->search))
        {
            throw new InvalidArgumentException('Не передан обязательный параметр запроса $search');
        }

        if(empty($this->search->getQuery()))
        {
            return false;
        }


        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();


        $dbal
            ->select('article.id as article_id')
            ->addSelect('article.title as article_title')
            ->addSelect('article.content as article_content')
            ->from(Article::class, 'article');

        $search = str_replace('-', ' ', $this->search->getQuery());
        /** Очистить поисковую строку от все НЕ буквенных/числовых символов */
        $search = preg_replace('/[^ a-zа-яё\d]/ui', '', $search);

        //        $searchBuilder = $dbal->createSearchQueryBuilder($this->search);

        $this->logger->info(sprintf('Строка поиска: %s', $search));

        /** Задаем префикс и суффикс для реализации варианта "содержит" */

        $search = '*'.$search.'*';

        $resultArticles = $this->redisSearchIndexHandler
            ->handleSearchQuery($search, ArticleRedisSearchTag::TAG);

        if($this->redisSearchIndexHandler instanceof RedisSearchIndexHandler)
        {
            /** Товары */
            $dbal->where('article.id IN (:uuids)')
                ->setParameter(
                    key: 'uuids',
                    value: $resultArticles ? array_column($resultArticles, 'id') : [],
                    type: ArrayParameterType::STRING);
        }


        $this->maxResult ? $dbal->setMaxResults($this->maxResult) : $dbal->setMaxResults(self::MAX_RESULTS);

        return $dbal->fetchAllHydrate(AllArticleResult::class);

    }


}