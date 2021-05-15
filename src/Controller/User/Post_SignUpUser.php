<?php


namespace App\Controller\User;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\AnalyticsService;
use App\Services\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Post_SignUpUser
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private EmailService $emailService,
        private AnalyticsService $analyticsService,
    )
    {
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function __invoke(Request $request): User | JsonResponse
    {
        if($this->userRepository->findOneBy(['email' => $request->get('email')])) {
            return new JsonResponse('[Error] Email Already Exists', Response::HTTP_CONFLICT);
        }

        if(strlen($request->get('username')) < 2) {
            return new JsonResponse('[Error] Username is too short', Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setUsername($request->get('username'));
        $user->setEmail($request->get('email'));

        $this->userRepository->persist($user);
        $this->entityManager->flush();

        $this->emailService->onUserCreated($user);
        $this->analyticsService->onUserCreated();

        return $user;
    }
}