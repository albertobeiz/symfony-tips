<?php


namespace App\SymfonyEventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class OnException_MapToResponseCode implements EventSubscriberInterface
{
    const EXCEPTION_TO_CODE = [
        \InvalidArgumentException::class => Response::HTTP_BAD_REQUEST
    ];

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException'
        ];
    }

    public function onException(ExceptionEvent $event) {
        $throwable = $event->getThrowable();
        $message = $throwable->getMessage();

        $code = self::EXCEPTION_TO_CODE[get_class($throwable)] ?? Response::HTTP_INTERNAL_SERVER_ERROR;
        $event->setResponse(new Response($message, $code));
    }
}