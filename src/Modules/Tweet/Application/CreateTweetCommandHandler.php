<?php


namespace App\Modules\Tweet\Application;


use App\Modules\Shared\Infrastructure\CommandHandler;
use App\Modules\Tweet\Domain\Tweet;
use App\Modules\Tweet\Infrastructure\TweetRepository;
use App\Modules\User\Domain\User;
use App\Modules\User\Infrastructure\UserRepository;
use InvalidArgumentException;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateTweetCommandHandler implements CommandHandler
{
    public function __construct(
        private TweetRepository $tweetRepository,
        private UserRepository $userRepository,
        private MessageBusInterface $eventBus
    )
    {
    }

    public function __invoke(CreateTweetCommand $command)
    {
        $user = $this->userRepository->find($command->userUuid);

        $tweet = new Tweet(
            $command->uuid,
            $user,
            $command->text
        );
        $this->tweetRepository->persist($tweet);

        foreach ($tweet->getDomainEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}