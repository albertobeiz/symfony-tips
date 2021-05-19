<?php

namespace App\Modules\Shared\Infrastructure;

use App\Modules\Shared\Domain\DomainEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DomainEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method DomainEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method DomainEvent[]    findAll()
 * @method DomainEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventStore extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DomainEvent::class);
    }

    public function push(DomainEvent $DomainEvent)
    {
        $this->getEntityManager()->persist($DomainEvent);
    }
}
