<?php


namespace App\Modules\Tweet\Application;


use App\Modules\Shared\Infrastructure\Command;
use Symfony\Component\Uid\Uuid;

class CreateTweetCommand implements Command
{
    public function __construct(
        public Uuid $uuid,
        public Uuid $userUuid,
        public string $text
    ){}
}