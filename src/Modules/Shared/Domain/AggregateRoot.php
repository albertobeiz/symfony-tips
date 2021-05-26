<?php


namespace App\Modules\Shared\Domain;

use Doctrine\ORM\Mapping\MappedSuperclass;

/** @MappedSuperclass */
abstract class AggregateRoot
{
    protected array $domainEvents = [];

    final public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }

    final protected function record(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }
}