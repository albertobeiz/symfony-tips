<?php


namespace App\Modules\Feed\Application;


use App\Modules\Shared\Infrastructure\Command;
use App\Modules\Shared\Infrastructure\Query;
use Symfony\Component\Uid\Uuid;

class UserFeedQueryWithProjectionQuery implements Query
{
    public function __construct(
        public Uuid $userUuid
    ){}
}