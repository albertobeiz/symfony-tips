<?php


namespace App\Modules\Shared\Application;


use App\Modules\Shared\Infrastructure\EventStore;
use App\Modules\Shared\Infrastructure\QueryHandler;

class QueryDomainEventsHandler extends QueryHandler
{
    public function __construct(
        private EventStore $eventStore
    )
    {
    }

    public function __invoke(QueryDomainEvents $query): array
    {
        return $this->eventStore->createQueryBuilder('e')
            ->select('e')
            ->getQuery()
            ->getArrayResult();
    }
}