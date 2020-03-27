<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpClient\HttpClient;
use JMS\Serializer\Serializer;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GithubUserProvider implements UserProviderInterface
{

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function loadUserByUsername($username)
    {
        $client = HttpClient::create();

        $url = 'https://api.github.com/user?access_token='.$username;

        $response = $this->client->get($url);
        $res = $response->getBody()->getContents();
        $userData = $this->serializer->deserialize($res, 'array', 'json');

        if (!$userData) {
            throw new \LogicException('Did not managed to get your user info from Github.');
        }

        return new User($userData['login'], $userData['name'], $userData['email'], $userData['avatar_url'], $userData['html_url']);
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
        return 'App\Entity\User' === $class;
    }
}