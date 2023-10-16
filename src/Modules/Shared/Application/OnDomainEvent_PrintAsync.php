<?php


namespace App\Modules\Shared\Application;


use App\Modules\Shared\Domain\DomainEvent;
use App\Modules\Shared\Infrastructure\EventHandler;

class OnDomainEvent_PrintAsync implements EventHandler
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