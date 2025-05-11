<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Article\UseCase\Admin\NewEdit\Tests;

use BaksDev\Article\Entity\Article;
use BaksDev\Article\UseCase\Admin\NewEdit\ArticleDTO;
use BaksDev\Article\UseCase\Admin\NewEdit\ArticleHandler;
use BaksDev\Support\UseCase\Admin\New\SupportHandler;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group article
 */
#[When(env: 'test')]
class ArticleNewTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $article = $em->getRepository(Article::class)
            ->findOneBy(['type' => TypeProfileUid::TEST]);

        if($article)
        {
            $em->remove($article);
        }

        $em->flush();
        $em->clear();
    }


    public function testUseCase(): void
    {

        /** ArticleDTO */
        $ArticleDTO = new ArticleDTO();
        $ArticleDTO->setTitle('New Test Title');
        self::assertSame('New Test Title', $ArticleDTO->getTitle());

        $ArticleDTO->setType(new TypeProfileUid(TypeProfileUid::TEST));
        self::assertSame((new TypeProfileUid(TypeProfileUid::TEST))->getTypeProfileValue(), $ArticleDTO->getType()->getTypeProfileValue());

        $ArticleDTO->setContent('New Test Content');
        self::assertSame('New Test Content', $ArticleDTO->getContent());

        /** @var ArticleHandler $ArticleHandler */
        $ArticleHandler = self::getContainer()->get(ArticleHandler::class);
        $handle = $ArticleHandler->handle($ArticleDTO);

        self::assertTrue(($handle instanceof Article), $handle.': Ошибка Article');

    }
}