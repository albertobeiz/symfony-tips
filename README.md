## Symfony Tips #09 - Catch exceptions in an Event Subscriber

🖥 **Symfony Tips:** Quick and practical tricks to develop solid backend systems.

Now the final step (for now), errors should also be part of your domain code. It's better to throw exceptions than returning Response objects.

### This is OK

```php

    public function __invoke(Request $request): User | Response
    {
        if($this->userRepository->findOneBy(['email' => $request->get('email')])) {
            return new Response('[Error] Email Already Exists', Response::HTTP_CONFLICT);
        }
    
        if(strlen($request->get('username')) < 2) {
            return new Response('[Error] Username is too short', Response::HTTP_BAD_REQUEST);
        }
        .
        .
        .
    }
 ```   

### This is better

Let's throw standard exceptions for simplicity. In your app you should create your own exceptions like UserEmailAlreadyExists, but this works ok.

```php

    public function __invoke(Request $request): User
    {
        if($this->userRepository->findOneBy(['email' => $request->get('email')])) {
            throw new InvalidArgumentException('[Error] Email Already Exists');
        }
    
        if(strlen($request->get('username')) < 2) {
            throw new InvalidArgumentException('[Error] Username is too short');
        }
        .
        .
        .
    }
```  

And we catch them in a new Symfony Event Subscriber, then map the Exception class to an http code.

```php

    class OnException_MapToResponseCode implements EventSubscriberInterface
    {
        const EXCEPTION_TO_HTTP_CODE = [
            \InvalidArgumentException::class => Response::HTTP_BAD_REQUEST
        ];
    
        public static function getSubscribedEvents(): array
        {
            return [
                KernelEvents::EXCEPTION => 'onException'
            ];
        }
    
        public function onException(ExceptionEvent $event) {
            $throwable = $event->getThrowable();
            $message = $throwable->getMessage();
    
            $code = self::EXCEPTION_TO_CODE[get_class($throwable)] ?? Response::HTTP_INTERNAL_SERVER_ERROR;
            $event->setResponse(new Response($message, $code));
        }
    }
```  

### Why?

Use Cases should not return http responses. What if you use this Use Case in a Symfony Command? Symfony Commands don't speak http.

Doing it this way allows us to execute our Use Cases in multiple applications.

### What about the Request?

It's an **http request**!!! It's **technology**!! It's **infrastructure**! You should **remove it**!

You are correct **BUT** removing it right now will take time and more complex changes.

We would have to add a new level of indirection and have a SignUpController call a SignUpUseCase for example and in the Controller transform the Request to primitives (ints, strings...) to pass them to the UseCase...

**Everything is a tradeoff** and at this stage we can live with this one. I'll explore this changes in future tips when discussing CQRS.

You can re-run **test.sh** to see that everything keeps working.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/09)!

Next Tip -> [Symfony Tips #10 - Test your Controllers](https://github.com/albertobeiz/symfony-tips/tree/10)

Previous Tip -> [Symfony Tips #08 - Flush in an Event Subscriber](https://github.com/albertobeiz/symfony-tips/tree/08)
