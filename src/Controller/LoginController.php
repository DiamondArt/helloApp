<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
    /**
     * Undocumented function
     *
     * @return void
     * @Route("/api/login")
     */
    public function login()
    {
        return new JsonResponse("ok",Response::HTTP_OK,[],true);
    }
}