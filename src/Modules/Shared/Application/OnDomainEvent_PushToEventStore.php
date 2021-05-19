<?php


namespace App\Modules\Shared\Application;


use App\Modules\Shared\Domain\DomainEvent;
use App\Modules\Shared\Infrastructure\EventStore;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class OnDomainEvent_PushToEventStore implements MessageSubscriberInterface
{
    public function __construct(
        private EventStore $eventStore
    )
    {
    }

    public function __invoke(DomainEvent $domainEvent)
    {
        echo '[Event Store] Pushing event ' . $domainEvent->getType() . "\n";
        $this->eventStore->push($domainEvent);
    }

    public static function getHandledMessages(): iterable
    {
        yield DomainEvent::class => [
            'from_transport' => 'sync',
        ];
    }
}