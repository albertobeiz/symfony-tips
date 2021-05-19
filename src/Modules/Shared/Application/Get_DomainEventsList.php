<?php


namespace App\Modules\Shared\Application;


use App\Modules\Shared\Infrastructure\EventStore;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Get_DomainEventsList
{
    public function __construct(
        private EventStore $eventStore
    )
    {
    }

    #[Route('/events', name: 'Get_DomainEventsList', methods: ['GET'])]
    public function __invoke(): array
    {
        return $this->eventStore->createQueryBuilder('e')
            ->select('e')
            ->getQuery()
            ->getArrayResult();
    }
}