<?php
namespace App\Controller;

use App\Entity\AuthToken;
use App\Entity\Credentials;
use App\Service\TokenGenerator;
use App\Form\Type\CredentialsType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Oka\RESTRequestValidatorBundle\Service\ErrorResponseFactory;

class AuthTokenController extends AbstractController
{
    /**
     * @Route("/auth-tokens", methods={"POST"})
     */
    public function postAuthTokensAction(Request $request,UserRepository $repo, UserPasswordEncoderInterface $encoder,EntityManagerInterface $em,
    JWTTokenManagerInterface $JWTManager)
    {
        $data = json_decode($request->getContent(), true);

        $credentials = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credentials);

        $form->submit($data);

       // $em = $managerRegistry->getManager();
        $em = $this->getDoctrine()->getManager();
        $user = $repo->findOneByUsername($credentials->getLogin());

        if (!$user) { // L'utilisateur n'existe pas
            return new JsonResponse("user not exist", Response::HTTP_BAD_REQUEST,[],true);
        }

        $isPasswordValid = $encoder->isPasswordValid($user, $credentials->getPassword());

        if (!$isPasswordValid) { // Le mot de passe n'est pas correct
            return new JsonResponse("bad password", Response::HTTP_BAD_REQUEST,[],true);
        }

        $authToken = new AuthToken();
        $token = $JWTManager->create($user);
        $authToken->setPayload($token);
        $authToken->setUser($user);

        $em->persist($authToken);
        $em->flush();

        return new JsonResponse([
			'token' => $authToken->getPayload(),
			'username' => $credentials->getLogin(),
		],Response::HTTP_OK);
    }

}
