<?php

namespace BaksDev\Article\UseCase\Admin\New;

use BaksDev\Article\Entity\Invariable\ArticleInvariableInterface;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleInvariableDTO implements ArticleInvariableInterface
{
    /**  Профиль */
    #[Assert\Uuid]
    private ?UserProfileUid $profile = null;

    public function getProfile(): ?UserProfileUid
    {
        return $this->profile;
    }

    public function setProfile(?UserProfileUid $profile): self
    {
        $this->profile = $profile;
        return $this;
    }
}