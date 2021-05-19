<?php


namespace App\Modules\Mailing\Application;


use App\Modules\User\Domain\UserCreated;
use App\Services\EmailService;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class OnUserCreated_SendWelcomeEmail implements MessageSubscriberInterface
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