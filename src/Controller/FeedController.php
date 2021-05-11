<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FeedController
{
    #[Route('/feed/', name: 'my_feed', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return new JsonResponse([]);
    }

    #[Route('/feed/{username}', name: 'user_feed', methods: ['GET'])]
    public function create(): JsonResponse
    {
        return new JsonResponse([]);
    }
}