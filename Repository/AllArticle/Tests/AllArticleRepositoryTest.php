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

namespace BaksDev\Article\Repository\AllArticle\Tests;

use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Article\Repository\AllArticle\AllArticleInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group article
 */
class AllArticleRepositoryTest extends KernelTestCase
{
    public function testUseCase(): void
    {

        /** @var AllArticleInterface $AllArtilceRepositoryInterface */
        $AllArticleRepositoryInterface = self::getContainer()->get(AllArticleInterface::class);

        $response =  $AllArticleRepositoryInterface
            ->search(new SearchDTO())
            ->findPaginator();

        if(!empty($response->getData()))
        {
            $current = current($response->getData());

            self::assertTrue(array_key_exists("id", $current));
            self::assertTrue(array_key_exists("title", $current));
            self::assertTrue(array_key_exists("name", $current));
            self::assertTrue(array_key_exists("content", $current));
        }

        self::assertTrue(true);
    }
}