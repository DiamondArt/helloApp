<?php
namespace App\Controller;

use App\Entity\User;
use App\Service\Mailer;
use App\Service\TokenGenerator;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $mailer, $encoder,$validator, $serializer,$tokenGenerate,$managerRegistry;

    public function __construct(Mailer $mailer,TokenGenerator $tokenGenerate,SerializerInterface $serializer,ValidatorInterface $validator,UserPasswordEncoderInterface $encoder,ManagerRegistry $managerRegistry)
    {
        $this->mailer = $mailer;
        $this->encoder = $encoder;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->tokenGenerate = $tokenGenerate;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * create user function
     *
     * @param Request $request
     * @Route("/registration", name="userRegister", methods={"POST"})
     */
    public function create(Request $request)
    {
        $data = $request->getContent();
        $entityManager = $this->managerRegistry->getManager();

        try{
             $user = $this->serializer->deserialize($data, User::class,'json');
             $errors = $this->validator->validate($user,null, ['create']);

             if(count($errors)){
                 return new JsonResponse($errors, Response::HTTP_BAD_REQUEST,[],true);
             }

             if($user->getRoles() == ['ROLE_USER'])
             {
                 $user->setLocked(false);
                 $user->setEnabled(false);
                 $user->setPropertiesVerified(false);
             }

            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $user->setConfirmationToken($this->tokenGenerate->getRandomSecureToken());
            $this->mailer->sendConfirmationEmail($user);

            $entityManager->persist($user);
            $entityManager->flush();

        }catch(BadResponse $e) {

            $this->logErr($e,$errors);
            $response = $e->getResponse();

            if(500 === ($response->getStatutCode())) {

                return $this->get('oka_rest_request_validator.error_response.factory')->create($this->get('translator')->trans('http_error.unexpected_error', [], 'error'), 500 , null, [], 500);
            }
        }
            return new JsonResponse('',Response::HTTP_CREATED,[],true);
    }

    /**
     * Undocumented function
     *
     * @param UserRepository $user
     * @param string $id
     * @Route("/profile", name="getUserProfile", methods={"GET"})
     */
    public function show(UserRepository $user,$id)
    {
        $user = $repo->find($id);

        if(null!=$user) {
            $user = $this->serializer->serialize($user, 'json',['groups' => ['summary']]);

        } else {
            return $this->get('oka_rest_request_validator.error_response.factory')->create($this->get('translator')->trans('user.not_found', [], 'error'), 404 , null, [], 404);
        }
        return new JsonResponse($user,Response::HTTP_OK,[], true);
    }

    /**
     * update user information function
     *
     * @param Request $request
     * @param string $id
     * @Route("/updateProfile/{id}", name="update")
     */
    public function update(Request $request,$id)
    {
        $data = $request->getContent();
        $manager = $this->managerRegistry->getManager();

        $user = $repo->find($id);
        $this->serializer->deserialize($data, Person::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

        if(null!=$user) {
            $errors = $this->validator->validate($user, null, ['update']);

            if (count($errors)) {
                return new JsonResponse($errors, Response::HTTP_BAD_REQUEST,[],true);
            }
            $manager->persist($user);
            $manager->flush();

        } else {

            return $this->get('oka_rest_request_validator.error_response.factory')->create($this->get('translator')->trans('user.not_found', [], 'error'), 404 , null, [], 404);
        }

        return new JsonResponse('',Response::HTTP_OK,[], true);
    }

    /**
     * @Route("/deleteProfile/{id}", name="delete_user")
     * @param User $user
    */
    public function delete(UserRepository $repo,$id)
    {
        $user = $repo->find($id);
        $manager = $this->managerRegistry->getManager();

        if(null!=$user) {
            $manager->remove($user);
            $manager->flush();

        } else {
            return $this->get('oka_rest_request_validator.error_response.factory')->create($this->get('translator')->trans('user.not_found', [], 'error'), 404 , null, [], 404);
        }
        return new JsonResponse([],Response::HTTP_OK,[], true);
    }

}