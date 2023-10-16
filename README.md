## Symfony Tips #07 - Serialize output in an Event Subscriber

Like decoding input, serializing output is not a business concern. Let's remove it from our Use Case.

### This is OK

```php

    public function __invoke(Request $request): Response
    {
        .
        .
        .
        $response = $this->serializer->serialize($user, 'json');
        return new Response(
            $response,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
```

### This is better

Move serialization to a Subscriber so we can return regular Objects (or void) from our Use Cases. Then you can configure whatever your need and return a JSON string.

```php

    public function __invoke(Request $request): Response
    {
        .
        .
        .
        return $user;
    }
```   

```php

    namespace App\SymfonyEventSubscriber;
    
    class OnView_Serialize implements EventSubscriberInterface
    {
        public function __construct(
            private SerializerInterface $serializer
        )
        {
        }
    
        public static function getSubscribedEvents()
        {
            return [
                KernelEvents::VIEW => 'onView'
            ];
        }
    
        public function onView(ViewEvent $event): void
        {
            $result = $event->getControllerResult();
            if(!$result) {
                $event->setResponse(new Response());
                return;
            }
    
            $context = SerializationContext::create();
            $context->enableMaxDepthChecks();
            $context->setSerializeNull(true);
    
            $response = new Response(
                $this->serializer->serialize($result, 'json', $context),
                Response::HTTP_OK,
                [
                    'Content-Type' => 'application/json'
                ]
            );
    
            $event->setResponse($response);
        }
    }
```  

This Subscriber **only executes if the Use Case does not return a Response**, so we can skip it if needed.

### Why?

To remove more non business code and to centralice all serialization logic. All of this will help us when we start testing our Use Cases, if there is no dependency, we don't have to mock it!

You can re-run **test.sh** to see that everything keeps working.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/07)!

Next Tip -> [Symfony Tips #08 - Flush in an Event Subscriber](https://github.com/albertobeiz/symfony-tips/tree/08)

Previous Tip -> [Symfony Tips #06 - Decode input in an Event Subscriber](https://github.com/albertobeiz/symfony-tips/tree/09)
