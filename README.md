## Symfony Tips #18 - Expose a Domain Events API

"Hey, can you please make an endpoint 'users/count' to show it in the analytics panel"

"I need you to call my service every time a user signs up, to give them symfopoints"

"How long does it take on average for a user to create her first symfotwit?"

All of this scenarios, and many more, are solved with our Domain Events Log.

And we don't have to do anything!

"Here is the log '/events', do what you need"

### This is OK

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

### This is Better

```php

        public function __invoke(Request $request): User
        {
           .
           .
           .
            return $user;
        }
```

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

And if you run **test.sh** you'll see an example at the end

```

    [Test.sh] Query Domain Events...
    
    [
        {
            "uuid": {
                "uid": "97207cd3-8486-4fda-b44a-57158365a0a8"
            },
            "aggregateUuid": {
                "uid": "550a59e1-81a7-4f50-a78d-ef4ece422088"
            },
            "occurredOn": "2021-05-19T20:02:51+00:00",
            "type": "com.symfony_tips.user.user_created",
            "data": {
                "userUuid": "550a59e1-81a7-4f50-a78d-ef4ece422088",
                "username": "aa",
                "email": "a@a.a"
            },
            "classType": "UserCreated"
        },
        {
            "uuid": {
                "uid": "f427d031-5951-4f1e-b168-223631ef6d6a"
            },
            "aggregateUuid": {
                "uid": "572f89a7-1bc8-4fe4-aad4-047d4fa28491"
            },
            "occurredOn": "2021-05-19T20:02:51+00:00",
            "type": "com.symfony_tips.user.user_created",
            "data": {
                "userUuid": "572f89a7-1bc8-4fe4-aad4-047d4fa28491",
                "username": "aa",
                "email": "b@a.a"
            },
            "classType": "UserCreated"
        },
        {
            "uuid": {
                "uid": "6e7e7a54-af8e-4f02-bcce-60a5f41003ae"
            },
            "aggregateUuid": {
                "uid": "ca7e3fc1-015b-4f8c-9c2c-4bd1a776f10a"
            },
            "occurredOn": "2021-05-19T20:02:51+00:00",
            "type": "com.symfony_tips.user.user_created",
            "data": {
                "userUuid": "ca7e3fc1-015b-4f8c-9c2c-4bd1a776f10a",
                "username": "aa",
                "email": "c@a.a"
            },
            "classType": "UserCreated"
        }
    ]
``` 

🥳 And now you can tell the analytics team to query your log with their Python script or whatever they want.

Now we have all the tools we need, we can do things when Domain Events happen (sync or async) and we can delegate it to the other service using the EventStore.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/18)!

Next Tip -> [Symfony Tips #19 - CQRS - Command Bus](https://github.com/albertobeiz/symfony-tips/tree/19)

Previous Tip -> [Symfony Tips #17 - Store your Domain Events](https://github.com/albertobeiz/symfony-tips/tree/17)
