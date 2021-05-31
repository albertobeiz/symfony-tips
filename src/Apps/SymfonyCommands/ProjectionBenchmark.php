<?php


namespace App\Apps\SymfonyCommands;


use App\Modules\Feed\Application\UserFeedQueryWithProjectionQuery;
use App\Modules\Follow\Application\FollowUserCommand;
use App\Modules\Shared\Infrastructure\QueryBus;
use App\Modules\Tweet\Application\CreateTweetCommand;
use App\Modules\Tweet\Application\UserFeedQueryWithJoinsQuery;
use App\Modules\User\Application\CreateUserCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class ProjectionBenchmark extends Command
{
    protected static $defaultName = 'tips:projection-benchmark';

    public function __construct(
        string $name = null,
        private MessageBusInterface $commandBus,
        private QueryBus $queryBus,
        private EntityManagerInterface $entityManager
    )
    {
        ini_set("memory_limit", "4096M");
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $steps = [10, 25, 50, 100, 150, 200];

        $joinTimes = [];
        $projectionTimes = [];

        foreach ($steps as $stepCount) {
            $command = $this->getApplication()->find('doctrine:schema:drop');
            $arguments = ['-f' => true, '-q' => true,];
            $greetInput = new ArrayInput($arguments);
            $command->run($greetInput, new NullOutput());

            $command = $this->getApplication()->find('doctrine:schema:update');
            $arguments = ['--force' => true, '--quiet' => true];
            $greetInput = new ArrayInput($arguments);
            $command->run($greetInput, new NullOutput());

            echo "Creando usuarios\n";
            $progressBar = new ProgressBar($output, $stepCount);
            $progressBar->setFormat('verbose');
            $progressBar->start();

            $i = 0;
            $uuids = [];
            while ($i++ < $stepCount) {
                $this->commandBus->dispatch(new CreateUserCommand(
                    $uuids[] = Uuid::v4(),
                    "user-$i",
                    "$i@$i" . uniqid()
                ));

                $progressBar->advance();
            }

            $this->entityManager->flush();
            $progressBar->finish();
            echo "\n";


            echo "Follows\n";
            $progressBar = new ProgressBar($output, $stepCount * $stepCount);
            $progressBar->setFormat('verbose');
            $progressBar->start();

            foreach ($uuids as $followerUuid) {
                foreach ($uuids as $followeeUuid) {
                    if ($followerUuid == $followeeUuid) {
                        continue;
                    }

                    $this->commandBus->dispatch(new FollowUserCommand(
                        Uuid::v4(),
                        $followerUuid,
                        $followeeUuid
                    ));

                    $progressBar->advance();
                }
                $this->entityManager->flush();
            }

            $progressBar->finish();
            echo "\n";


            echo "Tweets\n";
            $progressBar = new ProgressBar($output, $stepCount * $stepCount);
            $progressBar->setFormat('verbose');
            $progressBar->start();

            foreach ($uuids as $i => $uuid) {
                $shortUuid = explode('-', $uuid)[0];
                for ($t = 0; $t < $stepCount; $t++) {
                    $this->commandBus->dispatch(new CreateTweetCommand(
                        Uuid::v4(),
                        $uuid,
                        "User $shortUuid - Tweet $i"
                    ));

                    $progressBar->advance();
                }
                $this->entityManager->flush();
            }

            $progressBar->finish();
            echo "\n";

            echo "Query Feed using Joins\n";
            $start = microtime(true);
            $tweets = count($this->queryBus->query(new UserFeedQueryWithJoinsQuery($uuids[0])));
            $end = microtime(true);
            echo $joinTimes[] = round($end - $start, 2);
            echo " s $tweets tweets";

            echo "\nQuery Feed using Projection\n";
            $start = microtime(true);
            $tweets = count($this->queryBus->query(new UserFeedQueryWithProjectionQuery($uuids[0])));
            $end = microtime(true);
            echo $projectionTimes[] = round($end - $start, 2);
            echo " s $tweets tweets";
            echo "\n\n\n";
        }

        $output->writeln("Query UserFeed execution time (seconds)");
        $output->write("Users\t");
        foreach ($steps as $step) {
            $output->write("$step\t");
        }
        echo "\n";
        $output->write("Joins\t");
        foreach ($joinTimes as $time) {
            $output->write("$time\t");
        }
        echo "\n";
        $output->write("Proj.\t");
        foreach ($projectionTimes as $time) {
            $output->write("$time\t");
        }
        echo "\n";
        return Command::SUCCESS;
    }
}