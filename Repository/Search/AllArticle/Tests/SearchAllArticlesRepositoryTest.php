<?php

namespace BaksDev\Article\Repository\Search\AllArticle\Tests;

use BaksDev\Article\Repository\Search\AllArticle\SearchAllArticlesInterface;
use BaksDev\Core\Form\Search\SearchDTO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group articles-search
 */
//#[When(env: 'test')]
class SearchAllArticlesRepositoryTest extends KernelTestCase
{
    public function testSearchArticles()
    {
        /** @var SearchAllArticlesInterface $repository */
        $repository = self::getContainer()->get(SearchAllArticlesInterface::class);

        $search = new SearchDTO();
        //        $search->setQuery('triangle');
        //        $search->setQuery('tri');
        $search->setQuery('?;+tri');

        $result = $repository
            ->search($search)
            ->findAll();

        //        dd($result);
        dd(iterator_to_array($result));

        self::assertTrue(true);
    }

}