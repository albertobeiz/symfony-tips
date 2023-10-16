# Symfony Tips #21 - CQRS - Event Bus

Having a static EventBus is a very convenient and easy way of dispatching events but we are mixing different application layers and maybe having a global object that you can access anywhere in your code is not the best idea.

### This is OK
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

### This is better

Make your user extend a new AggregateRoot class
```php
    class User extends AggregateRoot
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
    
            $this->record(new UserCreated($uuid, $username, $email));
        }
 ```   

Aggregate roots can record DomainEvents
```php
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
```

And now in your command handler you can make the dispatch
```php
    class CreateUserCommandHandler implements CommandHandler
    {
        public function __construct(
            private UserRepository $userRepository,
            private MessageBusInterface $eventBus
        ){}
    
        public function __invoke(CreateUserCommand $command)
        {
            if ($this->userRepository->findOneBy(['email' => $command->email])) {
                throw new InvalidArgumentException('[Error] Email Already Exists');
            }
    
            $user = new User(
                $command->uuid,
                $command->username,
                $command->email
            );
            $this->userRepository->persist($user);
    
            foreach ($user->getDomainEvents() as $event) {
                $this->eventBus->dispatch($event);
            }
        }
    }
```

### Why?

To keep each responsibility at their layer. Entities shouldn't know there's an Event Bus, the same way they don't know there's a Repository.

That said I usually stick with the static Bus (it works and it's testable even if it's not "clean code") except if it's a greenfield project with an experienced Team.

Run **test.sh** and check that everything stays the same.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/21)!

Next Tip -> [Symfony Tips #22 - CQRS - Projections](https://github.com/albertobeiz/symfony-tips/tree/22)

Previous Tip -> [Symfony Tips #20 - CQRS - Query Bus](https://github.com/albertobeiz/symfony-tips/tree/20)
