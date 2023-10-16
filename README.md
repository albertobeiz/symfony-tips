## Symfony Tips #22 - CQRS - Projections

⚗️ Let's make an experiment!

We have 10 users, each of them follows the other 9 users, and creates 10 tweets. And then we'll query a user's feed.

And then we'll have 25 users...50, 100, 150, 200...and measure query time.

```php
echo "Query Feed using Joins";
$start = microtime(true);
$tweets = count($this->queryBus->query(new UserFeedQueryWithJoinsQuery($uuids[0])));
$end = microtime(true);
echo $joinTimes[] = round($end - $start, 2);

echo "Query Feed using Projection";
$start = microtime(true);
$tweets = count($this->queryBus->query(new UserFeedQueryWithProjectionQuery($uuids[0])));
$end = microtime(true);
echo $projectionTimes[] = round($end - $start, 2);
```

Check the benchmark code [here](https://github.com/albertobeiz/symfony-tips/blob/22/src/Apps/SymfonyCommands/ProjectionBenchmark.php)

### The use case

Pretty simple use case, just create a tweet
```php
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
```    

### Using Joins

To get a user's feed, query the tweets of the users she follows
```php
class UserFeedQueryWithJoinsQueryHandler extends QueryHandler
{
    public function __construct(
        private TweetRepository $tweetRepository,
    ){}

    public function __invoke(UserFeedQueryWithJoinsQuery $query)
    {
        return $this->tweetRepository->createQueryBuilder('t')
            ->join('t.user', 'u')
            ->join(Follow::class, 'f', Join::WITH, 'f.follower = :follower')
            ->setParameter('follower', $query->userUuid, 'uuid')
            ->where('t.user = f.followee')
            ->getQuery()
            ->getResult();
    }
}
```    

Using this approach, we can see query time increases with the number of users. Even if we have the best database server we can pay, this will end up being slow.

    Query UserFeed execution time (seconds)
    Users   10      25      50      100     150     200     
    Joins   0.01    0.01    0.02    0.09    0.23    0.43
    

### Using Projections

What if we pre-calculate each user feed using Domain Events? We can also store it in a different database that maybe is faster to query (Redis? Mongo? Elastic?), we'll use csv files in this example.

We'll use a new object FeedTweet that represents a tweet in a particular user feed. Every time a TweetCreated event is dispatched we add that tweet to the feed of all the followers of the creator.
```php
class OnTweetCreated_UpdateFollowersFeed implements EventHandler
{
    public function __construct(
        private FollowRepository $followRepository,
        private FeedTweetRepository $feedTweetRepository,
    ){}

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
```    

When persisting a FeedTweet we actually append it to a csv file.

Getting a user feed now is just reading her csv file.
```php
class FeedTweetRepository extends ServiceEntityRepository
{
    public function persist(FeedTweet $feedTweet)
    {
        $file = 'feeds/' . $feedTweet->getFeedOwner()->getUuid() . '.csv';
        $actual = file_exists($file) ?  file_get_contents($file) . "\n" : '';
        $actual .= implode(';', [
            $feedTweet->getUser()->getUuid(),
            $feedTweet->getUser()->getUsername(),
            $feedTweet->getText()
        ]);

        file_put_contents($file, $actual);
    }

    public function getUserFeed(Uuid $userUuid): array
    {
        $file = 'feeds/' . $userUuid . '.csv';
        return explode("\n", file_get_contents($file));
    }
}
```   

The use case just returns the feed.
```php
class UserFeedQueryWithProjectionQueryHandler extends QueryHandler
{
    public function __construct(
        private FeedTweetRepository $feedTweetRepository
    )
    {
    }

    public function __invoke(UserFeedQueryWithProjectionQuery $query)
    {
        return $this->feedTweetRepository->getUserFeed($query->userUuid);
    }
}
```    

Reading time remains fast no matter how many users there are. Writing now is slower because of all the csv files we have to update when a tweet is created, but think about how many times you read your feed vs how many times you post a tweet.

    Query UserFeed execution time (seconds)
    Users   10      25      50      100     150     200     
    Joins   0.01    0.01    0.02    0.09    0.23    0.43    
    Proj.   0       0       0       0       0       0
    

### Why?

Does this approach add complexity to your app? Absolutely. You don't need to use projections for every query you have, keep an eye on performance and think if this is a suitable solution when query performance starts degrading.

Run **php bin/console tips:projection-benchmark** if you want to try the benchmark yourself.

> Symfony tip completed 👍! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/22) and leave a ⭐️!

Previous Tip -> [Symfony Tips #21 - CQRS - Event Bus]([https://github.com/albertobeiz/symfony-tips/tree/21](https://github.com/albertobeiz/symfony-tips/tree/21))
