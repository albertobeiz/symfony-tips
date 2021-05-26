<?php

namespace App\Modules\User\Domain;

use App\Modules\Shared\Domain\AggregateRoot;
use App\Modules\Shared\Infrastructure\EventBus;
use App\Modules\User\Infrastructure\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User extends AggregateRoot
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private Uuid $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $email;

    public function __construct(
        Uuid $uuid,
        string $username,
        string $email
    )
    {
        $this->setUuid($uuid);
        $this->setUsername($username);
        $this->setEmail($email);

        $this->record(new UserCreated($uuid, $username, $email));
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        if (strlen($username) < 2) {
            throw new InvalidArgumentException('[Error] Username is too short');
        }

        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
