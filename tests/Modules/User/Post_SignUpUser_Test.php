<?php


namespace App\Tests\Modules\User;


use App\Modules\User\Application\Post_SignUpUser;
use App\Modules\User\Domain\User;
use App\Modules\User\Infrastructure\UserRepository;
use App\Services\AnalyticsService;
use App\Services\EmailService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class Post_SignUpUser_Test extends TestCase
{
    private Post_SignUpUser $useCase;
    private UserRepository $userRepository;
    private EmailService $emailService;
    private AnalyticsService $analyticsService;

    /**
     * @before
     */
    public function setup(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->emailService = $this->createMock(EmailService::class);
        $this->analyticsService = $this->createMock(AnalyticsService::class);

        $this->useCase = new Post_SignUpUser(
            $this->userRepository,
            $this->emailService,
            $this->analyticsService
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
        $this->useCase->__invoke(new Request());
    }

    public function testGivenShortUsernameThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->useCase->__invoke(new Request(['username' => 'a', 'email' => 'a@a.a']));
    }

    public function testGivenCorrectDataThenSaveUser()
    {
        $this->emailService->expects($this->once())->method('onUserCreated');
        $this->analyticsService->expects($this->once())->method('onUserCreated');

        $expectedUser = new User(
            Uuid::fromString('d9e7a184-5d5b-11ea-a62a-3499710062d0'),
            'username',
            'username@tips.com'
        );

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
}