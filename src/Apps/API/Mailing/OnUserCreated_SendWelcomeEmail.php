<?php


namespace App\Apps\API\Mailing;


use App\Modules\Shared\Infrastructure\EventHandler;
use App\Modules\User\Domain\UserCreated;
use App\Services\EmailService;

class OnUserCreated_SendWelcomeEmail implements EventHandler
{
    public function __construct(
        private EmailService $emailService
    )
    {
    }

    public function __invoke(UserCreated $domainEvent)
    {
        $this->emailService->send($domainEvent->email, 'Bienvenido a Twitfony');
    }

    public static function getHandledMessages(): iterable
    {
        yield UserCreated::class => [
            'from_transport' => 'async',
        ];
    }
}