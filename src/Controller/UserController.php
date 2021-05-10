<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController
{
    #[Route('/users', name: 'users_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return new JsonResponse([
            1, 2, 3, 4, 5
        ]);
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function create(): JsonResponse
    {
        return new JsonResponse([
            1, 2, 3, 4, 5
        ]);
    }

    #[Route('/follow', name: 'follow_user', methods: ['POST'])]
    public function follow(): JsonResponse
    {
        return new JsonResponse([
            1, 2, 3, 4, 5
        ]);
    }

    #[Route('/dm', name: 'user_dms', methods: ['GET'])]
    public function listDms(): JsonResponse
    {
        return new JsonResponse([
            1, 2, 3, 4, 5
        ]);
    }

    #[Route('/dm', name: 'send_dm', methods: ['POST'])]
    public function dm(): JsonResponse
    {
        return new JsonResponse([
            1, 2, 3, 4, 5
        ]);
    }
}