<?php


namespace App\Apps\API\Feed;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Get_UserFeed
{
    #[Route('/feed/{username}', name: 'user_feed', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([]);
    }
}