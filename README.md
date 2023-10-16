## Symfony Tips #08 - Flush in an Event Subscriber

To remove the EntityManagerInterface and any other database related code from our Use Case we will use another Symfony EventSubscriber.

### This is OK

```php

    public function __invoke(Request $request):  User | Response
    {
        .
        .
        .
        $this->userRepository->persist($user);
        $this->entityManager->flush();
        .
        .
        .
    }
 ```   

### This is better

```php

    public function __invoke(Request $request):  User | Response
    {
        .
        .
        .
        $this->userRepository->persist($user);
        .
        .
        .
    }
 ```   

```php

    class OnView_Flush implements EventSubscriberInterface
    {
        public function __construct(
            private EntityManagerInterface $entityManager
        )
        {
        }
    
        public static function getSubscribedEvents(): array
        {
            return [
                KernelEvents::VIEW => 'onView'
            ];
        }
    
        public function onView() {
            $this->entityManager->flush();
        }
    }
```    

### Why?

**flush()** is a database level operation and we want our Use Case to speak business.

This solution will call flush even if we don't do any database operation but it's a good tradeoff and the performance degradation is almost inexistent.

We are almost there! We have to deal with error messages (it's http and, guess what, http is not business code 😛) and we're ready to test it.

You can re-run **test.sh** to see that everything keeps working.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/08)!

Next Tip -> [Symfony Tips #09 - Catch exceptions in an Event Subscriber](https://github.com/albertobeiz/symfony-tips/tree/09)

Previous Tip -> [Symfony Tips #07 - Serialize output in an Event Subscriber](https://github.com/albertobeiz/symfony-tips/tree/07)
