## Symfony Tips #01 - One Use Case → One Controller

### This is OK

```php
class UserController
{
    #[Route('/users', name: 'users_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return new JsonResponse(...);
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function create(): JsonResponse
    {
        return new JsonResponse(...);
    }
    .
    . More functions...
    .
    #[Route('/dm', name: 'send_dm', methods: ['POST'])]
    public function dm(): JsonResponse
    {
        return new JsonResponse(...);
    }
}
```

### This is better

```php
class Get_UsersList
{
    #[Route('/users', name: 'users_list', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([]);
    }
}
.
. More classes...
.
class Post_DmUser
{
    #[Route('/dm', name: 'dm_user', methods: ['POST'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([]);
    }
}
```
### Why?
Take a look at this structure:

![](https://cdn.hashnode.com/res/hashnode/image/upload/v1620822273347/N9fUVQwj6.png?auto=compress,format&format=webp)

- What's this app about? What does it do?
- How big is it? 5 endpoints? 500? 5000? You can't know it without opening the Controllers

And now look at it after dividing the controllers:

![](https://cdn.hashnode.com/res/hashnode/image/upload/v1620821986084/2hYMD-EZY.png?auto=compress,format&format=webp)

Now we know it's not too big, 7 use cases, and that it's a Tweeter like app. All without looking at the code.

> Symfony tip completed 👍! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/01) and leave a ⭐️!

Next Tip -> [Symfony Tips #02 - Use folders to group your Use Cases](https://blog.albertobeiz.com/symfony-tips-02-use-folders-to-group-your-use-cases)
