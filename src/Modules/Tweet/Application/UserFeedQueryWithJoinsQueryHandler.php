<?php


namespace App\Modules\Tweet\Application;


use App\Modules\Follow\Domain\Follow;
use App\Modules\Shared\Infrastructure\QueryHandler;
use App\Modules\Tweet\Infrastructure\TweetRepository;
use Doctrine\ORM\Query\Expr\Join;

class UserFeedQueryWithJoinsQueryHandler extends QueryHandler
{
    public function __construct(
        private TweetRepository $tweetRepository,
    )
    {
    }

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