<?php


namespace App\Modules\User\Application;


use App\Modules\User\Domain\User;
use App\Modules\User\Infrastructure\UserRepository;
use App\Services\AnalyticsService;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class Post_SignUpUser
{
    public function __construct(
        private UserRepository $userRepository,
        private AnalyticsService $analyticsService,
    )
    {
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function __invoke(Request $request): User
    {
        if ($this->userRepository->findOneBy(['email' => $request->get('email')])) {
            throw new InvalidArgumentException('[Error] Email Already Exists');
        }

        $user = new User(
            Uuid::fromString($request->get('uuid', Uuid::v4())),
            $request->get('username'),
            $request->get('email')
        );
        $this->userRepository->persist($user);

        $this->analyticsService->onUserCreated();

        return $user;
    }
}