<?php

namespace BaksDev\Article\Type\Event;

use BaksDev\Core\Type\UidType\UidType;

class ArticleEventType extends UidType
{
    public function getClassType(): string
    {
        return ArticleEventUid::class;
    }

    public function getName(): string
    {
        return ArticleEventUid::TYPE;
    }
}