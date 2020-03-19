<?php
namespace App\Controller;

use App\Service\UserConfirmationService;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserConfirmController extends AbstractController
{
    /**
     * Undocumented function
     *
     * @param UserConfirmation $confirmationToken
     * @Route("/users/confirm/{token}", methods={"GET"}, name="confirmToken")
     */
    public function confirmAccount($token,UserConfirmationService $userConfirmationService)
    {
        $userConfirmationService->confirmUser($token);
        return $this->redirectToRoute('api_login');
    }
}
