<?php


namespace App\Tests\Controller\User;


use App\Controller\User\Post_SignUpUser;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\AnalyticsService;
use App\Services\EmailService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

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

        $this->userRepository->method('findOneBy')->willReturn(new User());
        $this->useCase->__invoke(new Request());
    }

    public function testGivenShortEmailThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->useCase->__invoke(new Request(['username' => 'a']));
    }

    public function testGivenCorrectDataThenSaveUser()
    {
        $this->emailService->expects($this->once())->method('onUserCreated');
        $this->analyticsService->expects($this->once())->method('onUserCreated');

        $expectedUser = (new User())->setUsername('username')->setEmail('username@tips.com');

        $this->userRepository->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($expectedUser));

        $this->useCase->__invoke(new Request([
            'username' => 'username',
            'email' => 'username@tips.com'
        ]));
    }
}