## Symfony Tips #17 - Store your Domain Events

We can store all dispatched events using a Subscriber, that way we'll have a log of events available to debug our app or to use them in the future (you never know!)

#### Make DomainEvent an Entity and map all Domain Events

```php

    <?php
    
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
``` 

### Create an Event Store (Repository)

```php

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
```   

#### Create the Sync Subscriber

```php

    class OnDomainEvent_PushToEventStore implements MessageSubscriberInterface
    {
        public function __construct(
            private EventStore $eventStore
        ){}
    
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
```   

#### Why?

Now you have a log with everything that happened in your app. We'll explore one posible use for this in the next tip.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/17)!

Next Tip -> [Symfony Tips #18 - Expose a Domain Events API](https://github.com/albertobeiz/symfony-tips/tree/18)

Previous Tip -> [Symfony Tips #16 - Send emails asynchronously](https://github.com/albertobeiz/symfony-tips/tree/16)
