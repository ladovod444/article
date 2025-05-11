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

namespace BaksDev\Article\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Article\Entity\Article;
use BaksDev\Article\UseCase\Admin\NewEdit\ArticleDTO;
use BaksDev\Article\UseCase\Admin\NewEdit\ArticleForm;
use BaksDev\Article\UseCase\Admin\NewEdit\ArticleHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[RoleSecurity('ROLE_SUPPORT_EDIT')]
final class EditController extends AbstractController
{
    #[Route('/admin/article/edit/{id}', name: 'admin.newedit.edit', methods: ['GET', 'POST'])]
    public function edit(
        #[MapEntity] Article $Article,
        Request $request,
        ArticleHandler $ArticleHandler,
    ): Response
    {

        $ArticleDTO = new ArticleDTO()
            ->setId($Article->getId())
            ->setTitle($Article->getTitle())
            ->setType($Article->getType())
            ->setContent($Article->getContent());

        /** Форма */
        $form = $this
            ->createForm(
                type: ArticleForm::class,
                data: $ArticleDTO,
                options: ['action' => $this->generateUrl(
                    route: 'article:admin.newedit.edit',
                    parameters: ['id' => $Article->getId()]
                ),]
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->refreshTokenForm($form);

            $handle = $ArticleHandler->handle($ArticleDTO);

            $this->addFlash(
                'page.edit',
                $handle instanceof Article ? 'success.edit' : 'danger.edit',
                'article.admin',
                $handle
            );

            return $this->redirectToRoute('article:admin.index');
        }

        return $this->render(['form' => $form->createView()]);
    }
}
