## Symfony Tips #04 - Use Repositories

🖥 **Symfony Tips:** Quick and practical tricks to develop solid backend systems.

Now a quick one. Let's take the first step to remove the **EntityManagerInterface** and use Repositories directly.

### This is OK

```php

    public function __invoke(Request $request): JsonResponse
    {
        .
        .
        .
        $repository = $this->entityManager->getRepository(User::class);
        if($repository->findOneBy(['email' => $body['email']])) {
        .
        .
        .
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        .
        .
        .
    }
```    

### This is Better

```php

    public function __invoke(Request $request): JsonResponse
    {
        .
        .
        .
        if($this->userRepository->findOneBy(['email' => $body['email']])) {
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

And add this function to **UserRepository**

```php

    public function persist(User $user)
    {
        $this->getEntityManager()->persist($user);
    }
``` 

### Why?

EntityManager is **not a business term**, and it's better (for everyone, not just devs) to use business related names, like UserRepository. Using Repositories will also help us with testing in the future.

But what about the **flush**? We will remove it soon! That way we can completely erase the **EntityManagerInterface** from our business code.

You can re-run **test.sh** to see that everything keeps working.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/04)!

Next Tip -> [Symfony Tips #05 - One line per service](https://github.com/albertobeiz/symfony-tips/tree/05)

Previous Tip -> [Symfony Tips #03 - Move internal dependencies to the constructor](https://github.com/albertobeiz/symfony-tips/tree/03)
