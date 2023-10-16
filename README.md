## Symfony Tips #20 - CQRS - Query Bus

Quick one! Queries should also have their own bus.

### This is OK
```php
        #[Route('/events', name: 'Get_DomainEventsList', methods: ['GET'])]
        public function __invoke(): array
        {
            return $this->eventStore->createQueryBuilder('e')
                ->select('e')
                ->getQuery()
                ->getArrayResult();
        }
```    

### This is better

Add a query bus to messenger.yaml config

            default_bus: command.bus
            buses:
                query.bus:
    
                command.bus:
                    middleware:
                        - doctrine_ping_connection
                        - doctrine_close_connection
                        - doctrine_transaction
    
                event.bus:
                    default_middleware: allow_no_handlers
                    middleware:
                        - doctrine_ping_connection
                        - doctrine_close_connection
                        - doctrine_transaction
    

Add Interfaces
```php
    
    interface QueryHandler
    {
    }
    
    interface Query
    {
    }
 ```   

Link each handler with its bus

services.yaml

        _instanceof:
            App\Modules\Shared\Infrastructure\QueryHandler:
                tags:
                    - { name: messenger.message_handler, bus: query.bus }
    

We need a class for the QueryBus to be able to get the answer (We should create one also for the CommandBus).
```php
    class QueryBus
    {
        use HandleTrait {
            handle as private handleQuery;
        }
    
        public function __construct(
            MessageBusInterface $queryBus
        )
        {
            $this->messageBus = $queryBus;
        }
    
        public function query(Query $query)
        {
            return $this->handleQuery($query);
        }
    }
```    

To move the domain logic inside our Modules we'll use a Query and a Query Handler
```php
    class QueryDomainEvents implements Query
    {
    }
    

    class QueryDomainEventsHandler extends QueryHandler
    {
        public function __construct(
            private EventStore $eventStore
        ){}
    
        public function __invoke(QueryDomainEvents $query): array
        {
            return $this->eventStore->createQueryBuilder('e')
                ->select('e')
                ->getQuery()
                ->getArrayResult();
        }
    }
```    

And we'll dispatch queries from our Api
```php
    class Get_DomainEventsList
    {
        public function __construct(
            private QueryBus $queryBus
        ){}
    
        #[Route('/events', name: 'Get_DomainEventsList', methods: ['GET'])]
        public function __invoke(): array
        {
            return $this->queryBus->query(new QueryDomainEvents());
        }
    }
```    

### Why?

As with Commands, we now have a single point to interact with our Domain.

Run **test.sh** and check that everything stays the same.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/20)!

Next Tip -> [Symfony Tips #21 - CQRS - Event Bus](https://github.com/albertobeiz/symfony-tips/tree/21)

Previous Tip -> [Symfony Tips #19 - CQRS - Command Bus](https://github.com/albertobeiz/symfony-tips/tree/19)
