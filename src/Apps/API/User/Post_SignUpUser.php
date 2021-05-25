<?php


namespace App\Apps\API\User;


use App\Modules\User\Application\CreateUserCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class Post_SignUpUser
{
    public function __construct(
        private MessageBusInterface $commandBus
    )
    {
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function __invoke(Request $request): Uuid
    {
        $uuid = Uuid::fromString($request->get('uuid', Uuid::v4()));
        $this->commandBus->dispatch(new CreateUserCommand(
            $uuid,
            $request->get('username'),
            $request->get('email')
        ));

        return $uuid;
    }
}