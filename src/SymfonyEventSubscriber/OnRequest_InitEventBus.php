<?php


namespace App\SymfonyEventSubscriber;


use App\Modules\Shared\EventBus;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;

class OnRequest_InitEventBus implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $eventBus
    )
    {
    }

    public static function getSubscribedEvents(): iterable
    {
        return [
            KernelEvents::REQUEST => 'onRequest'
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        EventBus::setEventBus($this->eventBus);
    }
}