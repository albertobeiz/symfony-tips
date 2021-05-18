<?php


namespace App\Modules\Shared;


use Symfony\Component\Messenger\MessageBusInterface;

class EventBus
{
    private static MessageBusInterface $eventBus;

    public static function dispatch(DomainEvent $domainEvent)
    {
        static::$eventBus->dispatch($domainEvent);
    }

    public static  function setEventBus(MessageBusInterface $eventBus)
    {
        static::$eventBus = $eventBus;
    }
}