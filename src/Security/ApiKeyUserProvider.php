<?php
namespace App\Security;



use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class ApiKeyUserProvider implements UserProviderInterface
{

    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }


    public function loadUserByUsername($username)
    {
        
        return new User(
            $username,
            null,
            // the roles for the user - you may choose to determine
            // these dynamically somehow based on the user
            ['ROLE_USER']
        );
    }

    public function refreshUser(UserInterface $user)
    {

        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException();
        }

        return $user;
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}

