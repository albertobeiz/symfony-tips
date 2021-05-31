<?php


namespace App\Modules\Tweet\Domain;


use App\Modules\Shared\Domain\DomainEvent;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Uid\Uuid;

/**
 * @Entity
 */
class TweetCreated extends DomainEvent
{
    public function __construct(
        public Uuid $uuid,
        public Uuid $userUuid,
        public string $text,
    )
    {
        parent::__construct($this->uuid);
    }

    function getType(): string
    {
        return 'com.symfony_tips.user.tweet_created';
    }

    function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'userUuid' => $this->userUuid,
            'text' => $this->text,
        ];
    }
}