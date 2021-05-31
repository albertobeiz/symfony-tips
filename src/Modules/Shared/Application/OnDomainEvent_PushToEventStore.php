<?php


namespace App\Modules\Shared\Application;


use App\Modules\Shared\Domain\DomainEvent;
use App\Modules\Shared\Infrastructure\EventHandler;
use App\Modules\Shared\Infrastructure\EventStore;

class OnDomainEvent_PushToEventStore implements EventHandler
{
    public function __construct(
        private EventStore $eventStore
    )
    {
    }

    public function __invoke(DomainEvent $domainEvent)
    {
        $this->eventStore->push($domainEvent);
    }

    public static function getHandledMessages(): iterable
    {
        yield DomainEvent::class => [
            'from_transport' => 'sync',
        ];
    }
}