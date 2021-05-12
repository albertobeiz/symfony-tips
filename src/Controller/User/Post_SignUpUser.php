<?php


namespace App\Controller\User;


use App\Services\EmailService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Post_SignUpUser
{
    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function __invoke(Request $request, EmailService $emailService): JsonResponse
    {
        $emailService->send('a@a.a', 'Bienvenido a Twitfony');
        return new JsonResponse([]);
    }
}