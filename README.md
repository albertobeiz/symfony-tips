## Symfony Tips #16 - Send emails asynchronously

We'll use our new Domain Events to remove the EmailService from our use case.

### This is OK

```php

        public function __invoke(Request $request): User
        {
            .
            .
            .
            $this->emailService->onUserCreated($user);
            $this->analyticsService->onUserCreated();
    
            return $user;
        }
```  

### This is better

```php

        public function __invoke(Request $request): User
        {
            .
            .
            .
            $this->analyticsService->onUserCreated();
    
            return $user;
        }
 ```   

```php

    class OnUserCreated_SendWelcomeEmail implements MessageSubscriberInterface
    {
        public function __construct(
            private EmailService $emailService
        )
        {
        }
    
        public function __invoke(UserCreated $domainEvent)
        {
            $this->emailService->send($domainEvent->email, 'Bienvenido a Twitfony');
        }
    
        public static function getHandledMessages(): iterable
        {
            yield UserCreated::class => [
                'from_transport' => 'async',
            ];
        }
    }
```  

That's it, magic 🧙 Now each time a UserCreated event is dispatched this Subscriber will be called. And you can make it sync or async just by changing the _from\_transport_ config.

Let's run **test.sh** to see it in action.

```

    [Test.sh] Sending new user requests
    
    [Event Bus - PrintSync] App\Modules\User\Domain\UserCreated
    [Analytics Service] Added User
    {
        "uuid": {
            "uid": "f5e2b737-81dc-4fd2-a08a-984c0993937b"
        },
        "username": "aa",
        "email": "a@a.a"
    }
    
    [Error] Email Already Exists
    
    [Error] Username is too short
    
    
    [Test.sh] Waiting before processing Async events...
    
    [Email Service] Send Bienvenido a Twitfony to a@a.a
    [Event Bus - PrintAsync] App\Modules\User\Domain\UserCreated
```

And don't forget to remove all email related code from the unit test.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/16)!

Next Tip -> [Symfony Tips #17 - Store your Domain Events](https://github.com/albertobeiz/symfony-tips/tree/17)

Previous Tip -> [Symfony Tips #15 - Dispatch Domain Events](https://github.com/albertobeiz/symfony-tips/tree/15)
