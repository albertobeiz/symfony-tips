<?php

namespace App\Modules\Follow\Domain;

use App\Modules\Shared\Domain\AggregateRoot;
use App\Modules\Tweet\Infrastructure\TweetRepository;
use App\Modules\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=TweetRepository::class)
 */
class Follow extends AggregateRoot
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
    private User $follower;

    /**
     * @ORM\ManyToOne(targetEntity="App\Modules\User\Domain\User")
     * @ORM\JoinColumn(referencedColumnName="uuid", onDelete="CASCADE")
     */
    private User $followee;

    public function __construct(
        Uuid $uuid,
        User $follower,
        User $followee,
    )
    {
        $this->setUuid($uuid);
        $this->setFollower($follower);
        $this->setFollowee($followee);

        $this->record(new UserFollowed($follower->getUuid(), $followee->getUuid()));
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

    public function getFollower(): User
    {
        return $this->follower;
    }

    public function setFollower(User $follower): void
    {
        $this->follower = $follower;
    }

    public function getFollowee(): User
    {
        return $this->followee;
    }

    public function setFollowee(User $followee): void
    {
        $this->followee = $followee;
    }
}
