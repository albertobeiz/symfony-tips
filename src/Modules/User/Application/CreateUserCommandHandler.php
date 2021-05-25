<?php


namespace App\Modules\User\Application;


use App\Modules\Shared\Infrastructure\CommandHandler;
use App\Modules\User\Domain\User;
use App\Modules\User\Domain\UserCreated;
use App\Modules\User\Infrastructure\UserRepository;
use InvalidArgumentException;

class CreateUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository)
    {
    }

    public function __invoke(CreateUserCommand $command)
    {
        if ($this->userRepository->findOneBy(['email' => $command->email])) {
            throw new InvalidArgumentException('[Error] Email Already Exists');
        }

        $user = new User(
            $command->uuid,
            $command->username,
            $command->email
        );
        $this->userRepository->persist($user);
    }
}