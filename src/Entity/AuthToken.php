<?php
namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()

 * @ORM\Table(name="auth_tokens")
 * @UniqueEntity(fields="payload")
 */
class AuthToken implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid_binary_ordered_time")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\Column(type="uuid_binary_ordered_time")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator")
     * @var \Ramsey\Uuid\UuidInterface
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @Groups({"auth-token"})
     * @var string
     */
    protected $payload;


    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @Groups({"auth-token"})
     * @var User
     */
    protected $user;


    public function getId(): string
    {
		return (string) $this->id;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function setPayload(string $payload) :self
    {
        $this->payload = $payload;
        return $this;

    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;

    }
}