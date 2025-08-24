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

namespace BaksDev\Article\Entity;

use BaksDev\Article\Entity\Event\ArticleEvent;
use BaksDev\Article\Type\Event\ArticleEventUid;
use BaksDev\Article\Type\Id\ArticleUid;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/* Article */

#[ORM\Entity]
#[ORM\Table(name: 'article')]
class Article
{
    /**
     * Идентификатор сущности
     */
    #[ORM\Id]
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: ArticleUid::TYPE)]
    private ArticleUid $id;

    /** Идентификатор события */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: ArticleEventUid::TYPE, unique: true)]
    private ArticleEventUid $event;


    /**
     * Тип профиля пользователей
     * @var TypeProfileUid
     */
//    #[Assert\Uuid]
//    #[ORM\Column(type: TypeProfileUid::TYPE, nullable: true)]
//    private ?TypeProfileUid $type;

    public function __construct()
    {
        $this->id = new ArticleUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }



//    public function getType(): ?TypeProfileUid
//    {
//        return $this->type;
//    }
//
//    public function setType(?TypeProfileUid $type): self
//    {
//        $this->type = $type;
//
//        return $this;
//    }


    public function getId(): ArticleUid
    {
        return $this->id;
    }

    public function getEvent(): ArticleEventUid
    {
        return $this->event;
    }

    public function setEvent(ArticleEventUid|ArticleEvent $event): self
    {
//        $this->event = $event;


        $this->event = $event instanceof ArticleEvent ? $event->getId() : $event;

        return $this;
    }



}