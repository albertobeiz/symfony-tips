<?php


namespace App\Modules\User\Domain;


use App\Modules\Shared\DomainEvent;
use Symfony\Component\Uid\Uuid;

class UserCreated extends DomainEvent
{
    public function __construct(
        public Uuid $uuid,
        public string $username,
        public string $email
    )
    {
    }
}