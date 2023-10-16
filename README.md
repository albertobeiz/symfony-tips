## Symfony Tips #03 - Move internal dependencies to the constructor

Now that we have a better folder structure let's start cleaning up our Use Cases. Our goal is to remove as much infrastucture code (stuff that is not pure PHP) as we can.

We will work with the **Post\_SignUpUser** use case.

To test our use case download the repo:


    git clone git@github.com:albertobeiz/symfony-tips.git
    

and checkout previous' tip branch


    git checkout 02
    

Then execute a test script


    sh test.sh
    

And it should output


    [Test.sh] Sending new user requests
    
    [Email Service] Send Bienvenido a Twitfony to a@a.a
    [Analytics Service] Added User
    {
        "id": 1,
        "username": "aa",
        "email": "a@a.a"
    }
    
    
    [Error] Email Already Exists
    
    [Error] Username is too short
    

You can navigate the code to see where this output comes from.

Now that we have it working, let's divide our dependencies:

### This is OK

```php

    class Post_SignUpUser
    {
        #[Route('/users', name: 'create_user', methods: ['POST'])]
        public function __invoke(
            Request $request,
            EntityManagerInterface $entityManager,
            SerializerInterface $serializer,
            EmailService $emailService,
            AnalyticsService $analyticsService,
        ): Response
        {
            $body = json_decode($request->getContent(), true);
    
            $repository = $entityManager->getRepository(User::class);
            .
            . More code
            .
            $response = $serializer->serialize($user, 'json');
            return new Response(
                $response,
                Response::HTTP_OK,
                ['Content-Type' => 'application/json']
            );
        }
    }
```

### This is better

```php

    class Post_SignUpUser
    {
        public function __construct(
            private EntityManagerInterface $entityManager,
            private SerializerInterface $serializer,
            private EmailService $emailService,
            private AnalyticsService $analyticsService,
        )
        {
        }
    
        #[Route('/users', name: 'create_user', methods: ['POST'])]
        public function __invoke(Request $request): Response
        {
            $body = json_decode($request->getContent(), true);
    
            $repository = $this->entityManager->getRepository(User::class);
            .
            . More code
            .
            $response = $this->serializer->serialize($user, 'json');
            return new Response(
                $response,
                Response::HTTP_OK,
                ['Content-Type' => 'application/json']
            );
        }
    }
```

### Why?

It's a clean way to separate things that come from **inside** our app and data that comes from the **outside** (the request).

This division will help us in the future when we divide our app using events and with testing 👌 You can re-run **test.sh** to see that everything keeps working.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/03)!

Next Tip -> [Symfony Tips #04 - Use Repositories](https://github.com/albertobeiz/symfony-tips/tree/04)

Previous Tip -> [Symfony Tips #02 - Use folders to group your Use Cases](https://github.com/albertobeiz/symfony-tips/tree/02)
