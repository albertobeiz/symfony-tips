<?php


namespace App\Modules\Tweet\Application;


use App\Modules\Shared\Infrastructure\Command;
use App\Modules\Shared\Infrastructure\Query;
use Symfony\Component\Uid\Uuid;

class UserFeedQueryWithJoinsQuery implements Query
{
    public function __construct(
        public Uuid $userUuid
    ){}
}