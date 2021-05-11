<?php


namespace App\Controller\User;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Post_SignUpUser
{
    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([]);
    }
}