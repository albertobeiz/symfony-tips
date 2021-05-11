<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Post_FollowUser
{
    #[Route('/follow', name: 'follow_user', methods: ['POST'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([]);
    }
}