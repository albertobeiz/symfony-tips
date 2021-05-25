<?php


namespace App\Apps\API\DM;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Post_DmUser
{
    #[Route('/dm', name: 'user_dms', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([]);
    }
}