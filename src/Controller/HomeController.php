<?php
namespace App\Controller;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * Undocumented function
     *
     * @return void
     * @Route("/api/homes", name="home")
     */
    public function index(SerializerInterface $serializer)
    {
         return $this->render('home.html.twig');
    }

     /**
     * Undocumented function
     *
     * @return void
     * @Route("/home", name="homes", methods={"GET"})
     */
    public function home(SerializerInterface $serializer)
    {

        $client = HttpClient::create();

        //$response = $client->request('GET', 'http://localhost:8000/api/users');
        $response = $client->request('GET', 'http://localhost:8000/api/users', [
            // use a different HTTP Basic authentication only for this request
            'auth_bearer' => $this->getUser(),
        
            // ...
        ]);
        $content = $response->getContent();
        $headers = $response->getHeaders();
        $tableHeaders = $serializer->encode($headers, 'json');

        var_dump($headers);
        die();
       // $regions = file_get_contents('http://localhost:8000/api/users');
        // $tableRegions = $serializer->decode($regions, 'json');

         //$objetRegions = $serializer->denormalize($tableRegions, 'App\Entity\Regions[]');
         $user = $serializer->deserialize($content, 'App\Entity\User[]','json');

         return $this->render('welcome.html.twig',[
            "users" => $user
        ]);
    }
}