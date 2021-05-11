<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Post_RegisterUser
{
    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function create(): JsonResponse
    {
        return new JsonResponse([
            1, 2, 3, 4, 5
        ]);
    }
}