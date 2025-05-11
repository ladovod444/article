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

namespace BaksDev\Article\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Article\Entity\Article;
use BaksDev\Article\UseCase\Admin\Delete\ArticleDeleteDTO;
use BaksDev\Article\UseCase\Admin\Delete\ArticleDeleteForm;
use BaksDev\Article\UseCase\Admin\Delete\ArticleDeleteHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[RoleSecurity('ROLE_SUPPORT_DELETE')]
final class DeleteController extends AbstractController
{
    #[Route('/admin/article/delete/{id}', name: 'admin.delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity] Article $Article,
        ArticleDeleteHandler $ArticleDeleteHandler,
    ): Response
    {

        $ArticleDeleteDTO = new ArticleDeleteDTO();
        $ArticleDeleteDTO->setId($Article->getId());

        $form = $this
            ->createForm(ArticleDeleteForm::class, $ArticleDeleteDTO, [
                'action' => $this->generateUrl(
                    'article:admin.delete',
                    ['id' => $Article->getId()]
                ),
            ])
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->refreshTokenForm($form);

            $handle = $ArticleDeleteHandler->handle($ArticleDeleteDTO);

            $this->addFlash(
                'page.delete',
                $handle instanceof Article ? 'success.delete' : 'danger.delete',
                'article.admin',
                $handle
            );

            return $handle instanceof Article ? $this->redirectToRoute('article:admin.index') : $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView(),]);
    }
}