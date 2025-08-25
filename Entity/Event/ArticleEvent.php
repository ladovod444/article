<?php

declare(strict_types=1);

namespace BaksDev\Article\Entity\Event;

use BaksDev\Article\Entity\Article;
use BaksDev\Article\Entity\Invariable\ArticleInvariable;
use BaksDev\Article\Entity\Modify\ArticleModify;
use BaksDev\Article\Type\Event\ArticleEventUid;
use BaksDev\Article\Type\Id\ArticleUid;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Support\Entity\Invariable\SupportInvariable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Entity\EntityState;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;


/* ArticleEvent */


#[ORM\Entity]
#[ORM\Table(name: 'article_event')]
class ArticleEvent extends EntityEvent
{
    /**
     * Идентификатор События
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: ArticleEventUid::TYPE)]
    private ArticleEventUid $id;

    /**
     * Идентификатор Корня Article
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: ArticleUid::TYPE)]
    private ?ArticleUid $main;

    /**  Постоянная величина */
    #[ORM\OneToOne(targetEntity: ArticleInvariable::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private ?ArticleInvariable $invariable = null;

    /** One To One */
    //#[ORM\OneToOne(mappedBy: 'event', targetEntity: ArticleLogo::class, cascade: ['all'])]
    //private ?ArticleOne $one = null;

    /**
     * Модификатор
     */
    #[ORM\OneToOne(mappedBy: 'event', targetEntity: ArticleModify::class, cascade: ['all'])]
    private ArticleModify $modify;

    /**
     * Переводы
     */
    //#[ORM\OneToMany(mappedBy: 'event', targetEntity: ArticleTrans::class, cascade: ['all'])]
    //private Collection $translate;

    #[Assert\NotBlank(message: 'Заголовок обязателен для заполнения')]
    #[ORM\Column(type: Types::STRING)]
    private ?string $title = null;

    #[Assert\NotBlank(message: 'Содержимое обязательно для заполнения')]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;


    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }


    public function __construct()
    {
        $this->id = new ArticleEventUid();
        $this->modify = new ArticleModify($this);

    }

    /**
     * Идентификатор события
     */

    public function __clone()
    {
        $this->id = clone new ArticleEventUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): ArticleEventUid
    {
        return $this->id;
    }

    /**
     * Идентификатор Article
     */
    public function setMain(ArticleUid|Article $main): void
    {
        $this->main = $main instanceof Article ? $main->getId() : $main;
    }

    public function getMain(): ?ArticleUid
    {
        return $this->main;
    }

    public function getDto($dto): mixed
    {
        if($dto instanceof ArticleEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof ArticleEventInterface)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function isInvariable(): bool
    {
        return $this->invariable instanceof ArticleInvariable;
    }

    public function setInvariable(ArticleInvariable|false $invariable): self
    {
        if($invariable instanceof ArticleInvariable)
        {
            $this->invariable = $invariable;
        }

        return $this;
    }

    public function getInvariable(): ?ArticleInvariable
    {
        return $this->invariable;
    }


}