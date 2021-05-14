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
        private SerializerInterface $serializer,
        private EmailService $emailService,
        private AnalyticsService $analyticsService,
    )
    {
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $body = json_decode($request->getContent(), true);

        if($this->userRepository->findOneBy(['email' => $body['email']])) {
            return new JsonResponse('[Error] Email Already Exists', Response::HTTP_CONFLICT);
        }

        if(strlen($body['username']) < 2) {
            return new JsonResponse('[Error] Username is too short', Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setUsername($body['username']);
        $user->setEmail($body['email']);

        $this->userRepository->persist($user);
        $this->entityManager->flush();

        $this->emailService->send($user->getEmail(), 'Bienvenido a Twitfony');
        $userCount = $this->analyticsService->getUsersCount();
        $this->analyticsService->setUsersCount($userCount + 1);

        $response = $this->serializer->serialize($user, 'json');
        return new JsonResponse($response);
    }
}