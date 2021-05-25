<?php


namespace App\Modules\Shared\Infrastructure;


use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class QueryBus
{
    use HandleTrait {
        handle as private handleQuery;
    }

    public function __construct(
        MessageBusInterface $queryBus
    )
    {
        $this->messageBus = $queryBus;
    }

    public function query(Query $query)
    {
        return $this->handleQuery($query);
    }
}