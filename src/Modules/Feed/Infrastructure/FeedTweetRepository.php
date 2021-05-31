<?php

namespace App\Modules\Feed\Infrastructure;

use App\Modules\Feed\Domain\FeedTweet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method FeedTweet|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeedTweet|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeedTweet[]    findAll()
 * @method FeedTweet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedTweetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedTweet::class);
    }

    public function persist(FeedTweet $feedTweet)
    {
        $file = 'feeds/' . $feedTweet->getFeedOwner()->getUuid() . '.csv';
        $actual = file_exists($file) ?  file_get_contents($file) . "\n" : '';
        $actual .= $feedTweet->getUser()->getUsername() . ";";
        $actual .= $feedTweet->getText();
        file_put_contents($file, $actual);
    }

    public function getUserFeed(Uuid $userUuid): array
    {
        $file = 'feeds/' . $userUuid . '.csv';
        return explode("\n", file_get_contents($file));
    }
}
