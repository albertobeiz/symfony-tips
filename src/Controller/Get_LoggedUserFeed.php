<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Get_LoggedUserFeed
{
    #[Route('/', name: 'my_feed', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return new JsonResponse([
            1,2,3,4,5
        ]);
    }
}