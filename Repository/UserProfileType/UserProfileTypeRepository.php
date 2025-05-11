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

namespace BaksDev\Article\Repository\UserProfileType;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Users\Profile\TypeProfile\Entity\Event\TypeProfileEvent;
use BaksDev\Users\Profile\TypeProfile\Entity\Info\TypeProfileInfo;
use BaksDev\Users\Profile\TypeProfile\Entity\Trans\TypeProfileTrans;
use BaksDev\Users\Profile\TypeProfile\Entity\TypeProfile;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class UserProfileTypeRepository implements UserProfileTypeInterface
{
    public function __construct(
        private DBALQueryBuilder $DBALQueryBuilder,
        private TranslatorInterface $translator
    ) {}

    public function findUserTypeProfiles(bool $addProfileNotSelected = true): array
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        /* TypeProfile */
        $dbal->select('profile.id');
        $dbal->addSelect('profile.event');

        $dbal->from(TypeProfile::class, 'profile');

        /* TypeProfile Event */
        $dbal->join(
            'profile',
            TypeProfileEvent::class,
            'profile_event',
            'profile_event.id = profile.event'
        );

        /* TypeProfile Translate */
        $dbal
            ->addSelect('profile_trans.name')
            ->join(
                'profile',
                TypeProfileTrans::class,
                'profile_trans',
                'profile_trans.event = profile.event AND profile_trans.local = :local'
            );

        $dbal
            ->addSelect('info.active')
            ->leftJoin(
                'profile',
                TypeProfileInfo::class,
                'info',
                'info.profile = profile.id'
            );


        $dbal->orderBy('profile_event.sort', 'ASC');

        $result = $dbal->fetchAllAssociative();

        $results = [];

        foreach($result as $item)
        {
            $results[] = new TypeProfileUid($item['id'], $item['name'], $item['event']);
        }

        /**
         * Добавляем элемент для возможности выбрать ответ с не выбранным типом профиля пользователя
         */
        if($addProfileNotSelected)
        {
            array_unshift($results,
                new TypeProfileUid(TypeProfileUid::TEST,
                    $this->translator->trans('filter.unchosen_profile', domain: 'article.admin'),
                    null
                ));
        }

        return $results;
    }


}