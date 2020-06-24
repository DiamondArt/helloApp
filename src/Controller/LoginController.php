<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterForm;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    /**
     * Undocumented function
     *
     * @return void
     * @Route("/api/login",name="api_login", methods={"POST"})
     */
    public function login()
    {
        return $this->render(
            'login.html.twig'
        );
    }

    /**
     * Undocumented function
     *
     * @return void
     * @Route("/test",name="login", methods={"GET"})
     */
    public function test()
    {
        return $this->render(
            'test.html.twig'
        );
    }

     /**
     * Undocumented function
     *
     * @return void
     * @Route("/api/registers")
     */
    public function register()
    {
        return $this->render(
            'register.html.twig', [
            ]);
    }

     /**
     * Undocumented function
     *
     * @return void
     * @Route("/api/profile", name="profile_page")
     */
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->json([
            'user' => $this->getUser()
        ]);
    }
}