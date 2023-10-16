## Symfony Tips #19 - CQRS - Command Bus

Let's remove the last bit of infrastructure code that lives in our use case.

### Complexity

I follow **all previous tips** in almost every backend I develop. They simplify structure, isolate different parts of the application and don't introduce much complexity.

Now **we are increasing complexity**. We will improve maintainability, testability and get tools to improve performance and observability but if the app is not big enough or the dev team is not experienced enough maybe it's not the best idea.

### CQRS?

**Command Query Responsibility Segregation** is a fancy name that means Commands (POSTs, PUTs, DELETEs) and Queries (GETs) are processed by different systems. In our case, we'll use two Symfony Messenger buses.

CQRS does not mean you have two databases, one for reading and one for writing. You can have them and usually this two patterns work together. But CQRS is about logic, not infrastructure.

### This is OK
```php
        #[Route('/users', name: 'create_user', methods: ['POST'])]
        public function __invoke(Request $request): User
        {
            if ($this->userRepository->findOneBy(['email' => $request->get('email')])) {
                throw new InvalidArgumentException('[Error] Email Already Exists');
            }
    
            $user = new User(
                Uuid::fromString($request->get('uuid', Uuid::v4())),
                $request->get('username'),
                $request->get('email')
            );
            $this->userRepository->persist($user);
    
            return $user;
        }
```    

### This is better

We need two different message buses, we had one for events and we need one for commands. Add them to messenger.yaml config
```
            default_bus: command.bus
            buses:
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
 ```   

Add Interfaces
```php
    interface EventHandler extends MessageSubscriberInterface
    {
    }
    
    interface CommandHandler
    {
    }
    
    interface Command
    {
    }
 ```   

Link each handler with its bus

services.yaml
```
        _instanceof:
            App\Modules\Shared\Infrastructure\CommandHandler:
                tags:
                    - { name: messenger.message_handler, bus: command.bus }
    
            App\Modules\Shared\Infrastructure\EventHandler:
                tags:
                    - { name: messenger.message_subscriber, bus: event.bus }
```  

And make previous event handlers implement our new interface
```php
    class OnUserCreated_SendWelcomeEmail implements EventHandler
    {
``` 

Now we have a clear point to separate our Apps from our Modules, let's move our Controllers to our API folder

annotations.yaml

    controllers:
        resource: ../../src/Apps/
        type: annotation
    

services.yaml

    App\Apps\:
            resource: '../src/Apps/'
            tags: ['controller.service_arguments']
    

Now we have our Apps folder with our API, maybe in the future we'll have a Backoffice or a ConsoleCli or Symfony commands...

![Captura de pantalla 2021-05-25 a las 16.13.27.png](https://cdn.hashnode.com/res/hashnode/image/upload/v1621952018159/bN2UA1YjF.png?auto=compress,format&format=webp)

To move the domain logic inside our Modules we'll use a Command and a Command Handler
```php
    class CreateUserCommand implements Command
    {
        public function __construct(
            public Uuid $uuid,
            public string $username,
            public string $email
        ){}
    }
    

    class CreateUserCommandHandler implements CommandHandler
    {
        public function __construct(
            private UserRepository $userRepository)
        {
        }
    
        public function __invoke(UserCreated $command)
        {
            if ($this->userRepository->findOneBy(['email' => $command->email])) {
                throw new InvalidArgumentException('[Error] Email Already Exists');
            }
    
            $user = new User(
                $command->userUuid,
                $command->username,
                $command->email
            );
            $this->userRepository->persist($user);
        }
    }
 ```   

And we'll dispatch commands from our Api
```php
    class Post_SignUpUser
    {
        public function __construct(
            private MessageBusInterface $commandBus
        )
        {
        }
    
        #[Route('/users', name: 'create_user', methods: ['POST'])]
        public function __invoke(Request $request): Uuid
        {
            $uuid = Uuid::fromString($request->get('uuid', Uuid::v4()));
            $this->commandBus->dispatch(new CreateUserCommand(
                $uuid,
                $request->get('username'),
                $request->get('email')
            ));
    
            return $uuid;
        }
    }
```  

Commands don't return anything but we could return the Uuid from our Controller to the front-end so it can query it if needed.

### Why?

Now we have a unique interface to interact with our Domain, we use Commands...from API, Backoffice, Console...just dispatch commands, business logic should stay the same wherever it's called from.

Run **test.sh** and check that everything stays the same, except the API response that it's now a Uuid.

### Unit test

Modify the test so it uses our new system
```php
        /**
         * @before
         */
        public function setup(): void
        {
            $this->messageBus = new InMemoryMessageBus();
            EventBus::setEventBus($this->messageBus);
    
            $this->userRepository = $this->createMock(UserRepository::class);
    
            $this->handler = new CreateUserCommandHandler(
                $this->userRepository
            );
        }
    
        public function testGivenUsedEmailThenThrowException()
        {
            $this->expectException(InvalidArgumentException::class);
    
            $this->userRepository->method('findOneBy')->willReturn(
                new User(
                    Uuid::v4(),
                    'username',
                    'username@tips.com'
                )
            );
    
            $this->handler->__invoke(new CreateUserCommand(
                    Uuid::v4(),
                    'username',
                    'username@tips.com')
            );
        }
```  

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/19)!

Next Tip -> [Symfony Tips #20 - CQRS - Query Bus](https://github.com/albertobeiz/symfony-tips/tree/20)

Previous Tip -> [Symfony Tips #18 - Expose a Domain Events API](https://github.com/albertobeiz/symfony-tips/tree/18)
