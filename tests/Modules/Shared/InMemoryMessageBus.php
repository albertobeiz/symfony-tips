<?php


namespace App\Tests\Modules\Shared;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class InMemoryMessageBus implements MessageBusInterface
{
    private array $dispatched = [];

    public function getDispatched(): array
    {
        return $this->dispatched;
    }

    public function dispatch($message, array $stamps = []): Envelope
    {
        $this->dispatched[] = $message;

        return new Envelope($message);
    }
}