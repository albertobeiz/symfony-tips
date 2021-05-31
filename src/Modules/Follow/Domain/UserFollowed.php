<?php


namespace App\Modules\Follow\Domain;


use App\Modules\Shared\Domain\DomainEvent;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Uid\Uuid;

/**
 * @Entity
 */
class UserFollowed extends DomainEvent
{
    public function __construct(
        public Uuid $followerUuid,
        public Uuid $followeeUuid
    ){
        parent::__construct($this->followerUuid);
    }

    function getType(): string
    {
        return 'com.symfony_tips.follow.user_followed';
    }

    function toArray(): array
    {
        return [
            'followerUuid' => $this->followerUuid,
            'followeeUuid' => $this->followeeUuid
        ];
    }
}