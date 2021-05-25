<?php


namespace App\Modules\User\Application;


use App\Modules\Shared\Infrastructure\Command;
use Symfony\Component\Uid\Uuid;

class CreateUserCommand implements Command
{
    public function __construct(
        public Uuid $uuid,
        public string $username,
        public string $email
    ){}
}