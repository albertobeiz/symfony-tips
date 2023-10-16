## Symfony Tips #06 - Decode input in an Event Subscriber

Let's remove some non-business related dependencies from the constructor. We'll start with input decoding using a Symfony EventSubscriber.

### This is OK

```php

    public function __invoke(Request $request): Response
    {
        $body = json_decode($request->getContent(), true);
    
         if($this->userRepository->findOneBy(['email' => $body['email']])) {
             return new Response('[Error] Email Already Exists', Response::HTTP_CONFLICT);
         }
        .
        .
        .
    }
```  

### This is better

We move the decoding logic to an EventSubscriber and use the request

```php

    public function __invoke(Request $request): Response
    {
         if($this->userRepository->findOneBy(['email' => $request->get('email')])) {
             return new Response('[Error] Email Already Exists', Response::HTTP_CONFLICT);
         }
        .
        .
        .
    }
```   

Decode JSON only if the request has the correct header

```php
    namespace App\SymfonyEventSubscriber;
    
    class OnRequest_DecodeBody implements EventSubscriberInterface
    {
        public static function getSubscribedEvents(): iterable
        {
            return [
                KernelEvents::REQUEST => 'onRequest'
            ];
        }
    
        public function onRequest(RequestEvent $event): void
        {
            $request = $event->getRequest();
            $contentType = $request->headers->get('Content-Type');
    
            if (str_starts_with($contentType, 'application/json')) {
                $data = json_decode($request->getContent(), true);
                $request->request->replace(is_array($data) ? $data : []);
            }
        }
    }
```    

### Why?

As stated before, we are removing as much logic as we can from Use Cases, to improve testability and reduce bugs, and parsing JSON is definitely not a business related problem.

You can re-run **test.sh** to see that everything keeps working.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/06)!

Next Tip -> [Symfony Tips #07 - Serialize output in an Event Subscriber](https://github.com/albertobeiz/symfony-tips/tree/07)

Previous Tip -> [Symfony Tips #05 - One line per service](https://github.com/albertobeiz/symfony-tips/tree/05)
