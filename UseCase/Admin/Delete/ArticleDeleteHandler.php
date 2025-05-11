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

namespace BaksDev\Article\UseCase\Admin\Delete;

use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Article\Entity\Article;
use BaksDev\Article\Messenger\ArticleMessage;
use Doctrine\ORM\EntityManagerInterface;

final class ArticleDeleteHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageDispatchInterface $messageDispatch
    ) {}

    /** @see */
    public function handle(ArticleDeleteDTO $command): Article|false
    {
        $Article = $this->entityManager->getRepository(Article::class)->find($command->getId());

        if(false === ($Article instanceof Article))
        {
            return false;
        }
        
        $this->entityManager->remove($Article);
        $this->entityManager->flush();

        /* Отправляем сообщение в шину */
        $message = new ArticleMessage($command->getId(), null, null, null);

        $this->messageDispatch->dispatch(
            message: $message,
            transport: 'article'
        );

        return $Article;
    }
}