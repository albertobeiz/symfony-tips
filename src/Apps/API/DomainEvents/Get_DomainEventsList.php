<?php


namespace App\Apps\API\DomainEvents;


use App\Modules\Shared\Application\QueryDomainEvents;
use App\Modules\Shared\Infrastructure\QueryBus;
use Symfony\Component\Routing\Annotation\Route;

class Get_DomainEventsList
{
    public function __construct(
        private QueryBus $queryBus
    )
    {
    }

    #[Route('/events', name: 'Get_DomainEventsList', methods: ['GET'])]
    public function __invoke(): array
    {
        return $this->queryBus->query(new QueryDomainEvents());
    }
}