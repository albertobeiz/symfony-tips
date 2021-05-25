<?php


namespace App\Modules\Shared\Infrastructure;

use Symfony\Component\Messenger\HandleTrait;

abstract class  QueryHandler
{
    use HandleTrait {
        handle as public query;
    }
}