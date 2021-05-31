<?php


namespace App\Modules\Feed\Application;


use App\Modules\Feed\Infrastructure\FeedTweetRepository;
use App\Modules\Shared\Infrastructure\QueryHandler;

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