<?php


namespace App\Modules\Shared\Domain;

use App\Modules\User\Domain\UserCreated;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity()
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="classType", type="string")
 * @DiscriminatorMap({ "UserCreated" = UserCreated::class })
 */
abstract class DomainEvent
{
    abstract function getType(): string;
    abstract function toArray(): array;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    protected Uuid $uuid;

    /**
     * @ORM\Column(type="uuid")
     */
    protected Uuid $aggregateUuid;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected DateTimeImmutable $occurredOn;

    /**
     * @ORM\Column(type="string")
     */
    protected string $type;

    /**
     * @ORM\Column(type="json")
     */
    protected array $data;

    public function __construct(
        Uuid $aggregateUuid
    )
    {
        $this->uuid = Uuid::v4();
        $this->aggregateUuid = $aggregateUuid;

        $this->occurredOn = new DateTimeImmutable();
        $this->type = $this->getType();
        $this->data = $this->toArray();
    }
}