<?php


namespace App\Modules\Follow\Application;


use App\Modules\Follow\Domain\Follow;
use App\Modules\Follow\Infrastructure\FollowRepository;
use App\Modules\Shared\Infrastructure\CommandHandler;
use App\Modules\Tweet\Domain\Tweet;
use App\Modules\Tweet\Infrastructure\TweetRepository;
use App\Modules\User\Domain\User;
use App\Modules\User\Infrastructure\UserRepository;
use InvalidArgumentException;
use Symfony\Component\Messenger\MessageBusInterface;

class FollowUserCommandHandler implements CommandHandler
{
    public function __construct(
        private FollowRepository $followRepository,
        private UserRepository $userRepository,
        private MessageBusInterface $eventBus
    )
    {
    }

    public function __invoke(FollowUserCommand $command)
    {
        $follower = $this->userRepository->find($command->followerUuid);
        $followee = $this->userRepository->find($command->followeeUuid);

        $tweet = new Follow(
            $command->uuid,
            $follower,
            $followee
        );
        $this->followRepository->persist($tweet);

        foreach ($tweet->getDomainEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}