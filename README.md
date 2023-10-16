## Symfony Tips #11 - Use Uuids as identifiers

Now we have to wait until _flush_ occurs to have a valid id for our Entities and sometimes we need that Id before that happens.

Also the urls to you resources will probably be consecutive numbers...bad idea.

### This is OK

```php

    class User
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        private $id;
```    

### This is better

```php

    class User
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="uuid")
         */
        private Uuid $uuid;
```    

### Why?

Setting your entities Ids it's an important task. It shouldn't be database work, your domain logic should take care of it.

Now we can also test it.

```php

        public function testGivenCorrectDataThenSaveUser()
        {
            $this->emailService->expects($this->once())->method('onUserCreated');
            $this->analyticsService->expects($this->once())->method('onUserCreated');
    
            $expectedUser = (new User())
                ->setUuid(Uuid::fromString('d9e7a184-5d5b-11ea-a62a-3499710062d0'))
                ->setUsername('username')
                ->setEmail('username@tips.com');
    
            $this->userRepository->expects($this->once())
                ->method('persist')
                ->with($this->equalTo($expectedUser));
    
            $returnedUser = $this->useCase->__invoke(new Request([
                'uuid' => 'd9e7a184-5d5b-11ea-a62a-3499710062d0',
                'username' => 'username',
                'email' => 'username@tips.com'
            ]));
    
            $this->assertEquals($expectedUser, $returnedUser);
        }
```    

You can run **php ./vendor/bin/phpunit** to run the tests.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/11)!

Next Tip -> [Symfony Tips #12 - Move all business logic to your entities](https://github.com/albertobeiz/symfony-tips/tree/12)

Previous Tip -> [Symfony Tips #10 - Test your Controllers](https://github.com/albertobeiz/symfony-tips/tree/10)

