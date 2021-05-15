<?php


namespace App\Controller\User;


use App\Entity\User;
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
    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function __invoke(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        EmailService $emailService,
        AnalyticsService $analyticsService,
    ): Response
    {
        $body = json_decode($request->getContent(), true);

        $repository = $entityManager->getRepository(User::class);
        if($repository->findOneBy(['email' => $body['email']])) {
            return new Response('[Error] Email Already Exists', Response::HTTP_CONFLICT);
        }

        if(strlen($body['username']) < 2) {
            return new Response('[Error] Username is too short', Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setUsername($body['username']);
        $user->setEmail($body['email']);

        $entityManager->persist($user);
        $entityManager->flush();

        $emailService->send($user->getEmail(), 'Bienvenido a Twitfony');
        $userCount = $analyticsService->getUsersCount();
        $analyticsService->setUsersCount($userCount + 1);

        $response = $serializer->serialize($user, 'json');
        return new Response(
            $response,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}