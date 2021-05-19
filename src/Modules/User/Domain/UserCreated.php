<?php


namespace App\Modules\User\Domain;


use App\Modules\Shared\Domain\DomainEvent;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Uid\Uuid;

/**
 * @Entity
 */
class UserCreated extends DomainEvent
{
    public function __construct(
        public Uuid $userUuid,
        public string $username,
        public string $email
    )
    {
        parent::__construct($this->userUuid);
    }

    function getType(): string
    {
        return 'com.symfony_tips.user.user_created';
    }

    function toArray(): array
    {
        return [
            'userUuid' => $this->userUuid,
            'username' => $this->username,
            'email' => $this->email
        ];
    }
}