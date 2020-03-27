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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthTokenController extends AbstractController
{
    /**
     * @Route("/auth-tokens", methods={"POST"})
     */
    public function postAuthTokensAction(Request $request,UserRepository $repo, UserPasswordEncoderInterface $encoder,EntityManagerInterface $em,TokenGenerator $tokenGenerate)
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
            return new JsonResponse("mots de passe incorrect", Response::HTTP_BAD_REQUEST,[],true);
        }

        $authToken = new AuthToken();
        $authToken->setPayload(base64_encode(random_bytes(50)));
        $authToken->setUser($user);

        $em->persist($authToken);
        $em->flush();

        return new JsonResponse([
			'payload' => $authToken->getPayload(),
			'username' => $credentials->getLogin(),
		],Response::HTTP_OK);
    }

    private function invalidCredentials()
    {
        return new JsonResponse(['message' => 'Invalid credentials'], Response::HTTP_BAD_REQUEST);
    }
}
