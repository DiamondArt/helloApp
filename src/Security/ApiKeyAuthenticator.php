<?php
namespace App\Security;

use App\Security\ApiKeyUserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface,AuthenticationFailureHandlerInterface
{
    public function createToken(Request $request, $providerKey)
    {
           // look for an apikey query parameter
           //$apiKey = $request->query->get('apikey');

           // or if you want to use an "apikey" header, then do something like this:
           $authorizationHeader = $request->headers->get('Authorization');
           // skip beyond "Bearer "
           $apiKey = substr($authorizationHeader, 7);

           if (!$apiKey) {
               throw new BadCredentialsException();

               // or to just skip api key authentication
               // return null;
           }

           return new PreAuthenticatedToken(
               'anon.',
               $apiKey,
               $providerKey
           );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $apiKey = $token->getCredentials();

        $user = $userProvider->loadUserByUsername($apiKey);

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            ['ROLE_USER']
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }


    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response("Authentication Failed :(", 401);
    }
}
