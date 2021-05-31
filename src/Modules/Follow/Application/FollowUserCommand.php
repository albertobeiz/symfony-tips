<?php


namespace App\Modules\Follow\Application;


use App\Modules\Shared\Infrastructure\Command;
use Symfony\Component\Uid\Uuid;

class FollowUserCommand implements Command
{
    public function __construct(
        public Uuid $uuid,
        public Uuid $followerUuid,
        public Uuid $followeeUuid
    ){}
}