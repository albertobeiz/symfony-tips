<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FeedController
{
    #[Route('/', name: 'my_feed', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return new JsonResponse([
            1,2,3,4,5
        ]);
    }

    #[Route('/{username}', name: 'user_feed', methods: ['GET'])]
    public function create(): JsonResponse
    {
        return new JsonResponse([
            1,2,3,4,5
        ]);
    }
}