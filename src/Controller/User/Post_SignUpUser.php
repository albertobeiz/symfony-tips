<?php


namespace App\Controller\User;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\AnalyticsService;
use App\Services\EmailService;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Post_SignUpUser
{
    public function __construct(
        private UserRepository $userRepository,
        private EmailService $emailService,
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

        if (strlen($request->get('username')) < 2) {
            throw new InvalidArgumentException('[Error] Username is too short');
        }

        $user = new User();
        $user->setUsername($request->get('username'));
        $user->setEmail($request->get('email'));

        $this->userRepository->persist($user);

        $this->emailService->onUserCreated($user);
        $this->analyticsService->onUserCreated();

        return $user;
    }
}