<?php


namespace App\Modules\Feed;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Get_LoggedUserFeed
{
    #[Route('/', name: 'my_feed', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([]);
    }
}