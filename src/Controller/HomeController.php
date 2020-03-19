<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * Undocumented function
     *
     * @return void
     * @Route("/homes")
     */
    public function index()
    {
        return $this->render('index.html.twig',
                            ['controller_name'=>'HomeController']);
    }
}