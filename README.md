## Symfony Tips #13 - Do validation in your setters

A quick one. As with business logic, move validations to your entities.

### This is OK

```php

        public function __invoke(Request $request): User
        {
            if ($this->userRepository->findOneBy(['email' => $request->get('email')])) {
                throw new InvalidArgumentException('[Error] Email Already Exists');
            }
    
            if (strlen($request->get('username')) < 2) {
                throw new InvalidArgumentException('[Error] Username is too short');
            }
            .
            .
            .
```    

### This is better

```php

        public function __invoke(Request $request): User
        {
            if ($this->userRepository->findOneBy(['email' => $request->get('email')])) {
                throw new InvalidArgumentException('[Error] Email Already Exists');
            }
            .
            .
            .
    
```

```php

    class User
    {
        public function setUsername(string $username)
        {
            if (strlen($username) < 2) {
                throw new InvalidArgumentException('[Error] Username is too short');
            }
    
            $this->username = $username;
        }
```  

### Why?

**Tell don't ask again**. Just tell your User the state you want and let the entity decide if that state is OK or not.

Duplicated emails is an **application state validation** so it stays at the application level.

You can run **php ./vendor/bin/phpunit** to run the tests.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/13)!

Next Tip -> [Symfony Tips #14 - Separate your application in modules](https://github.com/albertobeiz/symfony-tips/tree/14)

Previous Tip -> [Symfony Tips #12 - Move all business logic to your entities](https://github.com/albertobeiz/symfony-tips/tree/12)
