<?php

namespace BaksDev\Article\Entity\Invariable;

use BaksDev\Article\Entity\Event\ArticleEvent;
use BaksDev\Article\Type\Id\ArticleUid;
use BaksDev\Core\Entity\EntityReadonly;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* ArticleInvariable */

#[ORM\Entity]
#[ORM\Table(name: 'article_invariable')]
class ArticleInvariable extends EntityReadonly
{
    /**  Идентификатор Main */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: ArticleUid::TYPE)]
    private ArticleUid $main;

    /** Идентификатор События */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\OneToOne(targetEntity: ArticleEvent::class, inversedBy: 'invariable')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private ArticleEvent $event;

//    /** Тип профиля */
//    #[Assert\Uuid]
//    #[Assert\NotBlank]
//    #[ORM\Column(type: TypeProfileUid::TYPE)]
//    private TypeProfileUid $type;

    /**  Профиль */
    #[Assert\Uuid]
    #[ORM\Column(type: UserProfileUid::TYPE, nullable: true)]
    private ?UserProfileUid $profile = null;

//    /** Id Тикета */
//    #[Assert\NotBlank]
//    #[ORM\Column(type: Types::STRING, unique: true)]
//    private string $ticket;
//
//    /** Тема, заголовок или иная информация о предмете сообщения */
//    #[Assert\NotBlank]
//    #[ORM\Column(type: Types::STRING)]
//    private ?string $title = null;
//
//    /** Флаг запрета на ответ */
//    #[Assert\NotBlank]
//    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
//    private bool $reply = true;

    public function __construct(ArticleEvent $event)
    {
        $this->event = $event;
        $this->main = $event->getMain();
    }

    public function __toString(): string
    {
        return (string) $this->main;
    }

    public function setEvent(ArticleEvent $event): self
    {

        $this->event = $event;
        $this->main = $event->getMain();

        /** Генерируем идентификатор тикета */
//        $this->ticket = number_format(
//            (microtime(true) * 100),
//            0,
//            '.',
//            '.'
//        );

        return $this;
    }

//    public function getTitle(): ?string
//    {
//        return $this->title;
//    }
//
//    public function getType(): TypeProfileUid
//    {
//        return $this->type;
//    }

    public function getProfile(): ?UserProfileUid
    {
        return $this->profile;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof ArticleInvariableInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {
        if($dto instanceof ArticleInvariableInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}