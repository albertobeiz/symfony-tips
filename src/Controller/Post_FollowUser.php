<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Post_FollowUser
{
    #[Route('/follow', name: 'follow_user', methods: ['POST'])]
    public function follow(): JsonResponse
    {
        return new JsonResponse([
            1, 2, 3, 4, 5
        ]);
    }
}