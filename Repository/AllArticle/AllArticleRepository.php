<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Article\Repository\AllArticle;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Article\Entity\Article;
use BaksDev\Article\Form\Admin\Index\ArticleTypeProfileFilterDTO;
use BaksDev\Users\Profile\TypeProfile\Entity\Event\TypeProfileEvent;
use BaksDev\Users\Profile\TypeProfile\Entity\Trans\TypeProfileTrans;
use BaksDev\Users\Profile\TypeProfile\Entity\TypeProfile;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;

final class AllArticleRepository implements AllArticleInterface
{
    private SearchDTO|false $search = false;

    private ArticleTypeProfileFilterDTO|false $filter = false;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
    ) {}

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }

    public function filter(ArticleTypeProfileFilterDTO $filter): self
    {
        $this->filter = $filter;
        return $this;
    }

    /** Метод возвращает пагинатор Article */
    public function findPaginator(): PaginatorInterface
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal->select('article.id');
        $dbal->addSelect('article.title');

        $dbal
            ->addSelect('
            COALESCE(
                article.type,
                NULL
            ) AS type
        ');

        $dbal->addSelect('article.content');

        $dbal->from(Article::class, 'article');

//        $dbal->leftJoin(
//            'article',
//            TypeProfile::class,
//            'profile',
//            'profile.id = article.type'
//        );
//
//        /* TypeProfile Event */
//        $dbal->leftJoin(
//            'profile',
//            TypeProfileEvent::class,
//            'profile_event',
//            'profile_event.id = profile.event'
//        );
//
//        /* TypeProfile Translate */
//        $dbal
//            ->addSelect('profile_trans.name')
//            ->leftJoin(
//                'profile',
//                TypeProfileTrans::class,
//                'profile_trans',
//                'profile_trans.event = profile.event AND profile_trans.local = :local'
//            );

//        if($this->filter && !is_null($this->filter->getType()))
//        {
//            if($this->filter->getType() !== TypeProfileUid::TEST)
//            {
//                $dbal
//                    ->andWhere('article.type = :type')
//                    ->setParameter(
//                        'type',
//                        $this->filter->getType(),
//                        TypeProfileUid::TYPE
//                    );
//            }
//            else
//            {
//                /**
//                 * Выбрать ответ с НЕ выбранным типом профиля пользователя
//                 */
//                $dbal
//                    ->andWhere('article.type IS NULL')
//                ;
//            }
//        }

        /* Поиск */
        if($this->search->getQuery())
        {
            $dbal
                ->createSearchQueryBuilder($this->search)
                ->addSearchLike('article.content')
                ->addSearchLike('article.title');
        }

        $dbal
            ->orderBy('article.title');
        return $this->paginator->fetchAllAssociative($dbal);
    }

}