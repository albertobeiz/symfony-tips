## Symfony Tips #15 - Dispatch Domain Events

Now a long but very important one as using Domain Events will allow us to remove all dependencies from our use case (except the Repository)

### What's our goal

To dispatch an event when something happens, in this case when a user is created

```php

    class User
    {
        public function __construct(
            Uuid $uuid,
            string $username,
            string $email
        )
        {
            $this->setUuid($uuid);
            $this->setUsername($username);
            $this->setEmail($email);
    
            EventBus::dispatch(new UserCreated($uuid, $username, $email));
        }
 ```   

**A static class?** Yes, this will be our first approach, we'll improve it in future tips. Using a static EventBus allows us to send and event from wherever we want without changing any actual code. It's an easy strategy for adding events to legacy code.

**EventBus** is a simple class that stores a MessageBus

```php

    class EventBus
    {
        private static MessageBusInterface $eventBus;
    
        public static function dispatch(DomainEvent $domainEvent)
        {
            static::$eventBus->dispatch($domainEvent);
        }
    
        public static function setEventBus(MessageBusInterface $eventBus)
        {
            static::$eventBus = $eventBus;
        }
    }
```    

The problem with this approach is that we need to initialize the $eventBus variable, we'll use a Symfony Event Subscriber for that

```php

    class OnRequest_InitEventBus implements EventSubscriberInterface
    {
        public function __construct(
            private MessageBusInterface $eventBus
        ){}
    
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
```    

I'll use public attributes to avoid having getters, **tradeoff** 😛

```php

    class UserCreated extends DomainEvent
    {
        public function __construct(
            public Uuid $uuid,
            public string $username,
            public string $email
        )
        {
        }
    }
 ```   

Now we have a **Symfony Messenger** bus available anywhere in our app, with a simple config

```yaml

    framework:
        messenger:
            transports:
                async: '%env(MESSENGER_TRANSPORT_DSN)%'
                sync: 'sync://'
    
            routing:
                'App\Modules\Shared\DomainEvent': [sync, async]
```   

```

    // .env
    MESSENGER_TRANSPORT_DSN=doctrine://default
```  

To test our implementation lets create two subscribers, one for the sync transport and one for the async one.

```php

    class OnDomainEvent_PrintSync implements MessageSubscriberInterface
    {
        public function __invoke(DomainEvent $domainEvent)
        {
            echo '[Event Bus - PrintSync] ' . get_class($domainEvent) . "\n";
        }
    
        public static function getHandledMessages(): iterable
        {
            yield DomainEvent::class => [
                'from_transport' => 'sync',
            ];
        }
    }
``` 

```php

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
```   

If you run **test.sh** you'll see them in action

```

    [Test.sh] Sending new user requests
    
    [Event Bus - PrintSync] App\Modules\User\Domain\UserCreated
    [Email Service] Send Bienvenido a Twitfony to a@a.a
    [Analytics Service] Added User
    {
        "uuid": {
            "uid": "2955a072-edbd-4f2b-ad23-c7aab82efbed"
        },
        "username": "aa",
        "email": "a@a.a"
    }
    
    [Error] Email Already Exists
    
    [Error] Username is too short
    
    
    [Test.sh] Waiting before processing Async events...
    
    [Event Bus - PrintAsync] App\Modules\User\Domain\UserCreated
 ```   

### Unit tests

To be able to unit test our Use Case we need an InMemoryMessageBus so we can check what events have been dispatched

```php

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
```  

And in the test we need to initialize it and test the event

```php

        /**
         * @before
         */
        public function setup(): void
        {
            $this->messageBus = new InMemoryMessageBus();
            EventBus::setEventBus($this->messageBus);
        .
        .
        .
        }
    
        public function testGivenCorrectDataThenSaveUser()
        {
         .
         .
         .
        $this->assertEquals(
                new UserCreated(Uuid::fromString('d9e7a184-5d5b-11ea-a62a-3499710062d0'), 'username', 'username@tips.com'),
                $this->messageBus->getDispatched()[0]);
         }
 ```   

You can run **php ./vendor/bin/phpunit** to run the tests.

Pfew! That was intense. But now we can start decoupling our services.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/15)!

Next Tip -> [Symfony Tips #16 - Send emails asynchronously](https://github.com/albertobeiz/symfony-tips/tree/16)

Previous Tip -> [Symfony Tips #14 - Separate your application in modules](https://github.com/albertobeiz/symfony-tips/tree/14)
