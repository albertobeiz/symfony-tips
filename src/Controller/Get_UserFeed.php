<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Get_UserFeed
{
    #[Route('/{username}', name: 'user_feed', methods: ['GET'])]
    public function create(): JsonResponse
    {
        return new JsonResponse([
            1,2,3,4,5
        ]);
    }
}