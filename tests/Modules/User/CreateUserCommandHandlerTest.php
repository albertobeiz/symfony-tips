<?php


namespace App\Tests\Modules\User;


use App\Modules\Shared\Infrastructure\CommandHandler;
use App\Modules\Shared\Infrastructure\EventBus;
use App\Modules\User\Application\CreateUserCommand;
use App\Modules\User\Application\CreateUserCommandHandler;
use App\Modules\User\Domain\User;
use App\Modules\User\Domain\UserCreated;
use App\Modules\User\Infrastructure\UserRepository;
use App\Tests\Modules\Shared\InMemoryMessageBus;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class CreateUserCommandHandlerTest extends TestCase
{
    private CommandHandler $handler;
    private MessageBusInterface $messageBus;
    private UserRepository $userRepository;

    /**
     * @before
     */
    public function setup(): void
    {
        $this->messageBus = new InMemoryMessageBus();
        EventBus::setEventBus($this->messageBus);

        $this->userRepository = $this->createMock(UserRepository::class);

        $this->handler = new CreateUserCommandHandler(
            $this->userRepository
        );
    }

    public function testGivenUsedEmailThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->userRepository->method('findOneBy')->willReturn(
            new User(
                Uuid::v4(),
                'username',
                'username@tips.com'
            )
        );

        $this->handler->__invoke(new CreateUserCommand(
                Uuid::v4(),
                'username',
                'username@tips.com')
        );
    }

    public function testGivenShortUsernameThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->handler->__invoke(new CreateUserCommand(
                Uuid::v4(),
                'a',
                'a@a.a')
        );
    }

    public function testGivenCorrectDataThenSaveUser()
    {
        $expectedUser = new User(
            Uuid::fromString('d9e7a184-5d5b-11ea-a62a-3499710062d0'),
            'username',
            'username@tips.com'
        );

        $this->userRepository->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($expectedUser));

        $this->handler->__invoke(new CreateUserCommand(
            Uuid::fromString('d9e7a184-5d5b-11ea-a62a-3499710062d0'),
            'username',
            'username@tips.com'
        ));

        $this->assertEquals(
            (new UserCreated(
                Uuid::fromString('d9e7a184-5d5b-11ea-a62a-3499710062d0'),
                'username',
                'username@tips.com'
            ))->toArray(),
            $this->messageBus->getDispatched()[0]->toArray()
        );
    }
}