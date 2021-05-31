<?php

namespace App\Modules\Feed\Domain;

use App\Modules\Shared\Domain\AggregateRoot;
use App\Modules\Tweet\Infrastructure\TweetRepository;
use App\Modules\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=TweetRepository::class)
 */
class FeedTweet extends AggregateRoot
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
    private User $feedOwner;

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
        User $feedOwner,
        User $user,
        string $text,
    )
    {
        $this->setUuid($uuid);
        $this->setFeedOwner($feedOwner);
        $this->setUser($user);
        $this->setText($text);
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

    public function getFeedOwner(): User
    {
        return $this->feedOwner;
    }

    public function setFeedOwner(User $feedOwner): void
    {
        $this->feedOwner = $feedOwner;
    }
}
