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
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;

class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface,AuthenticationFailureHandlerInterface
{
    public function createToken(Request $request, $providerKey)
    {
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );
        $token = $extractor->extract($request);

         // or if you want to use an "apikey" header, then do something like this:
          $header = $request->headers->get('WWW-Authenticate');
          $apiKey = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1ODUzMzE2MzMsImV4cCI6MTU4NTMzMTY4Mywicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoibWVsaXNzYSJ9.QBXwP1g3-tASsK9FSUYSP_l18Sr1YX_ROq_4igwwGh5dUB6085KmuhhJdPZNLWkRNSEoqZ9xQ2Ftr6PN6lOufs39D8CRF3A1Ht3VnngEe1aLUEaqQSa5OvX58FQigE32HDYWX2JRzUNLM7IOy5ZX6agnTf4AVXh_eDROfpNGM1O3fQMUpsQ2PPw7w84XmA6ltERwvBl_dNaNLHsjbKwlTpxU062kNLmm-eKArATL_iPSPzmztaeYj10gsy8IrwlwjZ_RtF970jvvy1rh175j1lcGrr0r-DUarKcuFPiBMPeMUZw0-G6qV8cdfOObcogvTzdAWrLRoH32jBiVu-RmvsnlRl-JpPiA_2YQDLVX1YU2ivZFmMNyT2IyKkyaKamGUSgmUYcC7GXk91xUQgjpem5kOKeqlNtmw9v7fgQIIlf5rBiPaKQU8gCUGqSG0TDZlJPwdG0zvzx84epY7hGD5BZPKMn1X9XY3j9aYrJ1m1vBnkHQZgjSoJWx-6xb-eeOFji2i6R4G_G6nYWGrSoiC8Hwdig3kbAafVQaLehpD049SCriHhnfTn_VHZnRWPacR0pjoroW62QpGrGa9NvHqi99lE9uzgPw5ONS0Z0fOnPkTU87eOwbm1SaBtbipu3ZiBUGzk7lQcWrgSNWB2uVEIZZB2cU0dWw6ju8MeWBIk0";

          var_dump($header);
          die();
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
