<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Get_DMsByUser
{
    #[Route('/dm', name: 'user_dms', methods: ['GET'])]
    public function listDms(): JsonResponse
    {
        return new JsonResponse([
            1, 2, 3, 4, 5
        ]);
    }
}