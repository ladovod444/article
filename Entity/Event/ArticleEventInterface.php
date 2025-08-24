<?php

namespace BaksDev\Article\Entity\Event;

use BaksDev\Article\Type\Event\ArticleEventUid;

interface ArticleEventInterface
{
    public function getEvent(): ?ArticleEventUid;

    //    public function setId(ArticleEventUid ): self;
}