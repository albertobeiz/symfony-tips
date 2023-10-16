## Symfony Tips #05 - One line per service

We don't want to bloat our Use Cases with non-business code. To avoid it I follow a simple rule:

> A service can have only one line, one method call, and that method should be named like an Event Handler.

### This is OK

```php

    public function __invoke(Request $request): Response
    {
        .
        .
        .
        $this->emailService->send($user->getEmail(), 'Bienvenido a Twitfony');
        $userCount = $this->analyticsService->getUsersCount();
        $this->analyticsService->setUsersCount($userCount + 1);
        .
        .
        .
    }
```    

### This is better

```php

    public function __invoke(Request $request): Response
    {
        .
        .
        .
        $this->emailService->onUserCreated($user);
        $this->analyticsService->onUserCreated();
        .
        .
        .
    }
```  

```php

    // EmailService.php
    public function onUserCreated(User $user)
    {
        $this->send($user->getEmail(), 'Bienvenido a Twitfony');
    }
 ```   

```php

    // AnalyticsService.php
    public function onUserCreated() 
    {
        $userCount = $this->getUsersCount();
        $this->setUsersCount($userCount + 1);
    }
```  

### Why?

Now we have all email related logic in our EmailService and all our analytics in our Analytics Service, easier to find, reason about and test.

This rule will also help us when (or if) we decide to go for a more event-driven system using Domain Events to completely remove non business related code from the Use Case. We'll explore this approach in future Tips.

You can re-run **test.sh** to see that everything keeps working.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/05)!

Next Tip -> [Symfony Tips #06 - Decode input in an Event Subscriber](https://github.com/albertobeiz/symfony-tips/tree/06)

Previous Tip -> [Symfony Tips #04 - Use Repositories](https://github.com/albertobeiz/symfony-tips/tree/04)
