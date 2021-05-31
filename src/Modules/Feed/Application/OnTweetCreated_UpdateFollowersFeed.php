<?php


namespace App\Modules\Feed\Application;


use App\Modules\Feed\Domain\FeedTweet;
use App\Modules\Feed\Infrastructure\FeedTweetRepository;
use App\Modules\Follow\Infrastructure\FollowRepository;
use App\Modules\Shared\Infrastructure\EventHandler;
use App\Modules\Tweet\Domain\TweetCreated;
use Symfony\Component\Uid\Uuid;

class OnTweetCreated_UpdateFollowersFeed implements EventHandler
{
    public function __construct(
        private FollowRepository $followRepository,
        private FeedTweetRepository $feedTweetRepository,
    )
    {
    }

    public static function getHandledMessages(): iterable
    {
        yield TweetCreated::class => [
            'from_transport' => 'sync',
            'method' => 'onTweetCreated',
        ];
    }

    public function onTweetCreated(TweetCreated $event)
    {
        $follows = $this->followRepository->findBy(['followee' => $event->userUuid]);

        foreach ($follows as $follow) {
            $feedTweet = new FeedTweet(
                Uuid::v4(),
                $follow->getFollower(),
                $follow->getFollowee(),
                $event->text
            );
            $this->feedTweetRepository->persist($feedTweet);
        }
    }
}