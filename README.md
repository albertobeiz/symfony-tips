## Symfony Tips #12 - Move all business logic to your entities

We now have our logic in the Use Case using setters. It's better to move it inside our entities and let the Use Case just coordinate the different actors.

Following the **Tell don't ask** principle, create business functions in your entities (like **publish()** on Post or **approve()** on Payment) and perform all logic there, as well as validation (next tip).

Let's **refactor** (because tests won't change) our use case

### This is OK

```php

        public function __invoke(Request $request): User
        {
            .
            .
            .
            $user = new User();
            $user->setUuid(Uuid::fromString($request->get('uuid', Uuid::v4())));
            $user->setUsername($request->get('username'));
            $user->setEmail($request->get('email'));
            .
            .
            .
        }
 ```   

### This is better

```php

        public function __invoke(Request $request): User
        {
            .
            .
            .
            $user = new User(
                Uuid::fromString($request->get('uuid', Uuid::v4())),
                $request->get('username'),
                $request->get('email')
            );
            .
            .
            .
        }
  ```  

Move it tho the constructor in this scenario. If you want to have more semantic constructors search for **named constructors**

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
        }
```  

### Why?

We avoid duplication while hiding implementations details. It's easy to lose control of your entity state if you allow public setters to be called anywhere.

Maintain a clean and semantic interface to your domain objects and control your business logic at the core of your app.

You can run **php ./vendor/bin/phpunit** to run the tests.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/12)!

Next Tip -> [Symfony Tips #13 - Do validation in your setters](https://github.com/albertobeiz/symfony-tips/tree/13)

Previous Tip -> [Symfony Tips #11 - Use Uuids as identifiers](https://github.com/albertobeiz/symfony-tips/tree/11)
