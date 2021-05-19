<?php


namespace App\Modules\Shared\Application;


use App\Modules\Shared\Domain\DomainEvent;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class OnDomainEvent_PrintAsync implements MessageSubscriberInterface
{
    public function __invoke(DomainEvent $domainEvent)
    {
        echo '[Event Bus - PrintAsync] ' . get_class($domainEvent) . "\n";
    }

    public static function getHandledMessages(): iterable
    {
        yield DomainEvent::class => [
            'from_transport' => 'async',
        ];
    }
}