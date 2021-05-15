<?php


namespace App\Controller\User;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\AnalyticsService;
use App\Services\EmailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function __invoke(Request $request): User | Response
    {
        if($this->userRepository->findOneBy(['email' => $request->get('email')])) {
            return new Response('[Error] Email Already Exists', Response::HTTP_CONFLICT);
        }

        if(strlen($request->get('username')) < 2) {
            return new Response('[Error] Username is too short', Response::HTTP_BAD_REQUEST);
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