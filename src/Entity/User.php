<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Melissa Kouadio <melissa.kouadio@veone.net>
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Entity()
 * @ORM\Table(name="`user`")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="username", errorPath="username", message="user.username.already_used", groups={"create"})
 * @UniqueEntity(fields="email", errorPath="email", message="user.email.already_used", groups={"create", "Update"})
 */
class User implements AdvancedUserInterface, TimestampableInterface
{
    use TimestampableTrait;

    const ROLE_DEFAULT = 'ROLE_USER';
	const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid_binary_ordered_time")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator")
     * @Groups({"details"})
     * @var \Ramsey\Uuid\UuidInterface
     */
    protected $id;

   /**
     * @ORM\Column(name="username",type="string", unique=true)
     * @Groups({"summary", "details","create","update","auth-token"})
     * @Assert\NotBlank()
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(name="email",type="string", unique=true, nullable=true)
     * @Groups({"summary", "details","create","update","auth-token"})
	 * @Assert\Email()
     * @var string
     */
	 protected $email;

    /**
	 * @ORM\Column(type="string", length=148)
	 * @Assert\NotBlank()
	 * @Groups({"create","auth-token"})
	 * @var string
	 */
	protected $password;

	/**
	 * @ORM\Column(type="array")
	 * @var array
	 */

	protected $roles;
	/**
	 * @ORM\Column(type="boolean",options={"default": false})
	 * @Groups({"summary", "details","create"})
	 * @var bool
	 */
	protected $enabled;

	/**
	 * @ORM\Column(type="boolean")
	 * @Groups({"summary", "details","create"})
	 * @var bool
	 */
    protected $locked;

	/**
	 * @ORM\Column(name="properties_verified", type="smallint", options={"unsigned": true, "default": 0})
	 * @Groups({"summary", "details","create"})
	 * @var int
	 */
    protected $propertiesVerified;

    /**
	 * @ORM\Column(name="account_expires_at", type="datetime", nullable=true)
	 * @Groups({"details"})
	 * @var \DateTime
	 */
	protected $accountExpiresAt;

	/**
	 * @ORM\Column(name="credentials_expires_at", type="datetime", nullable=true)
	 * @Groups({"details"})
	 * @var \DateTime
	 */
	protected $credentialsExpiresAt;

	/**
	 * @ORM\Column(name="confirmation_token", type="string", unique=true, nullable=true)
	 * @var string
	 */
    protected $confirmationToken;

    /**
	 * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
	 * @var \DateTime
	 */
	protected $passwordRequestedAt;

	/**
	 * @ORM\Column(name="plain_password", type="string", nullable=true)
	 * @Groups({"passwordUpdate","create"})
	 * @var string
	 */
    private $plainPassword;

	public function __construct() {
		$this->roles = [];
		$this->propertiesVerified = 0;
		$this->enabled = false;
		$this->locked = false;
    }

    public function getId() :string
	{
		return (string) $this->id;
	}

	public function getUsername()
	{
		return $this->username;
    }

	public function setUsername(string $username) :self
	{
		$this->username = $username;
		return $this;
	}

	public function getEmail()
	{
		return $this->email;
    }

	public function setEmail(string $email) :self
	{
		$this->email = $email;
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

	public function hasRole(string $role) :bool
	{
		return in_array(strtoupper($role), $this->roles, true);
	}
    public function getRoles()
	{
		$roles = $this->roles;
		$roles[] = static::ROLE_DEFAULT;

		return array_unique($roles);
	}

	public function addRole(string $role) :self
	{
		$role = strtoupper($role);

		if (static::ROLE_DEFAULT !== $role) {
			if (false === in_array($role, $this->roles, true)) {
				$this->roles[] = $role;
			}
		}
		return $this;
	}

	public function setRoles(array $roles) :self
	{
		foreach ($roles as $role) {
			$this->addRole($role);
		}
		return $this;
	}

	public function removeRole(string $role) :self
	{
		$role = strtoupper($role);

		if (static::ROLE_DEFAULT !== $role) {
			if (false !== ($key = array_search($role, $this->roles, true))) {
				unset($this->roles[$key]);
				$this->roles = array_values($this->roles);
			}
		}
		return $this;
    }

    public function isEnabled() {
		return $this->enabled;
	}

	public function setEnabled(bool $enabled) :self
	{
		$this->enabled = $enabled;
		return $this;
	}

	public function isAccountNonLocked()
	{
		return !$this->locked;
	}

	public function getLocked()
	{
		return $this->locked;
    }

	public function setLocked(bool $locked) :self
	{
		$this->locked = $locked;
		return $this;
	}

	public function getPropertiesVerified()
	{
		return $this->propertiesVerified;
    }

    public function addPropertyVerified(int $propertiesVerified) :self
	{
		$this->propertiesVerified |= $propertiesVerified;
		return $this;
	}

	public function setPropertiesVerified(int $propertiesVerified) :self
	{
		$this->propertiesVerified = $propertiesVerified;
		return $this;
    }

	public function getAccountExpiresAt() :?\DateTime
	{
		return $this->accountExpiresAt;
	}

	public function setAccountExpiresAt(\DateTime $expiresAt = null) :self
	{
		$this->accountExpiresAt = $expiresAt;
		return $this;
	}

	public function isAccountNonExpired()
	{
		return $this->accountExpiresAt instanceof \DateTime ? $this->accountExpiresAt->getTimestamp() >= time() : true;
	}

	public function getCredentialsExpiresAt() :?\DateTime
	{
		return $this->credentialsExpiresAt;
	}

	public function setCredentialsExpiresAt(\DateTime $expiresAt = null) :self
	{
		$this->credentialsExpiresAt = $expiresAt;
		return $this;
	}

	public function isCredentialsNonExpired()
	{
		return $this->credentialsExpiresAt instanceof \DateTime ? $this->accountExpiresAt->getTimestamp() >= time() : true;
	}

	public function getConfirmationToken() :?string
	{
		return $this->confirmationToken;
	}

	public function setConfirmationToken(string $confirmationToken = null) :self
	{
		$this->confirmationToken = $confirmationToken;
		return $this;
	}

	public function getPasswordRequestedAt() :?\DateTime
	{
		return $this->passwordRequestedAt;
	}

	public function setPasswordRequestedAt(\DateTime $passwordRequestedAt = null) :self
	{
		$this->passwordRequestedAt = $passwordRequestedAt;
		return $this;
	}

	public function isPasswordRequestNonExpired(int $ttl) :bool
	{
		return $this->passwordRequestedAt instanceof \DateTime && $this->passwordRequestedAt->getTimestamp() + $ttl > time();
	}

	public function getSalt() {}

	public function getPlainPassword()
	{
		return $this->plainPassword;
	}

    public function setPlainPassword(string $plainPassword) :self
	{
		$this->plainPassword = $plainPassword;
        return $this;
	}

	public function eraseCredentials()
	{
		$this->plainPassword = null;
    }

    /**
	 * @Assert\Callback(groups={"create", "passwordUpdate"})
	 */
	public function validateUsername(ExecutionContextInterface $context)
	{
		switch (1) {
			case preg_match('#^auth$#i', $this->username):
				$builder = $context->buildViolation('user.username.invalid.word');
				break;

			case preg_match('#^(\+)?[0-9]+$#', $this->username):
				$builder = $context->buildViolation('user.username.invalid.composition');
				break;

			case preg_match('#[/[:blank:][:space:]@]#', $this->username):
				$builder = $context->buildViolation('user.username.invalid.characters');
				break;
		}

		if (true === isset($builder)) {
			$builder->atPath('username')
					->setInvalidValue($this->username)
					->addViolation();
		}
    }

    /**
	 * @Assert\Callback(groups={"create"})
	 */
	public function validatePassword(ExecutionContextInterface $context)
	{
		$length = strlen($this->plainPassword);

		switch (true) {
			case null === $this->plainPassword:
				break;

			case $length < 8:
				$builder = $context->buildViolation('user.password.short');
				break;

			case $length > 4096:
				$builder = $context->buildViolation('user.password.long');
				break;

			case strtolower($this->username) === strtolower($this->plainPassword):
				$builder = $context->buildViolation('user.password.equal_username');
				break;

			case strtolower($this->email) === strtolower($this->plainPassword):
				$builder = $context->buildViolation('user.password.equal_email');
				break;
		}

		if (true === isset($builder)) {
			$builder->atPath('[password]')
					->setInvalidValue($this->plainPassword)
					->addViolation();
		}
	}
}