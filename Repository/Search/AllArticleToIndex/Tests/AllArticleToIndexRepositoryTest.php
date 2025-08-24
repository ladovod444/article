<?php

namespace BaksDev\Article\Repository\AllArticleToIndex\Tests;

use BaksDev\Article\Repository\AllArticleToIndex\AllArticleToIndexInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group article-repo
 */
//#[When(env: 'test')]
class AllArticleToIndexRepositoryTest extends KernelTestCase
{
    public function testAllArticleToIndex()
    {
        /** @var AllArticleToIndexInterface $repository */
        $repository = self::getContainer()->get(AllArticleToIndexInterface::class);

        $result = $repository->toArray();

//        dd($result);

        self::assertTrue(true);
    }
}