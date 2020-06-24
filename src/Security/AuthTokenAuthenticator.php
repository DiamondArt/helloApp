<?php
namespace App\Security;

use App\Security\AuthTokenUserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;

class AuthTokenAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $jwtEncoder;

    public function __construct(JWTEncoderInterface $jwtEncoder,EntityManagerInterface $em)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->em = $em;
    }
    
    public function supports(Request $request)
    {
        return $request->headers->has('Bearer');
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        $authTokenHeader = $request->headers->get('Bearer');

        if (!$authTokenHeader) {
            throw new BadCredentialsException('Bearer header is required');
        }

        return array(
            'anon.',
            $authTokenHeader,
            $providerKey
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {

        if (!$userProvider instanceof AuthTokenUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of AuthTokenUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }
        $data = $this->jwtEncoder->decode($credentials);

        if ($data === false) {
            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }
        try {
            $data = $this->jwtEncoder->decode($credentials);

        } catch (JWTDecodeFailureException $e) {
            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }
        $authToken = $userProvider->getAuthToken($credentials);
        $user = $authToken->getUser();
        $username = $data['username'];

       //return $user;
        return $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // do nothing - let the controller be called
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse([
            'error' => 'Authenticate required'
        ], 401);

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}