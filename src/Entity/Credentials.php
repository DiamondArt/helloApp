<?php
namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class Credentials
{
    /**
	 * @Assert\NotBlank()
	 * @Groups({"auth-token"})
     * @var string
     */
    private $login;

    /**
	 * @Assert\NotBlank()
	 * @Groups({"auth-token"})
     * @var string
     */
    private $password;

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin(string $login) :self
    {
        $this->login = $login;
        return $this;

    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password) :self
    {
        $this->password = $password;
        return $this;

    }
}