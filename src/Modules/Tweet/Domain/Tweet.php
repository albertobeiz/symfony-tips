<?php

namespace App\Modules\Tweet\Domain;

use App\Modules\Shared\Domain\AggregateRoot;
use App\Modules\Tweet\Infrastructure\TweetRepository;
use App\Modules\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=TweetRepository::class)
 */
class Tweet extends AggregateRoot
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private Uuid $uuid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Modules\User\Domain\User")
     * @ORM\JoinColumn(referencedColumnName="uuid", onDelete="CASCADE")
     */
    private User $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $text;

    public function __construct(
        Uuid $uuid,
        User $user,
        string $text,
    )
    {
        $this->setUuid($uuid);
        $this->setUser($user);
        $this->setText($text);

        $this->record(new TweetCreated($uuid, $user->getUuid(), $text));
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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
