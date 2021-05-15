<?php


namespace App\SymfonyEventSubscriber;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnView_Serialize implements EventSubscriberInterface
{
    public function __construct(
        private SerializerInterface $serializer
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => 'onView'
        ];
    }

    public function onView(ViewEvent $event): void
    {
        $result = $event->getControllerResult();
        if(!$result) {
            $event->setResponse(new JsonResponse());
            return;
        }

        $context = SerializationContext::create();
        $context->enableMaxDepthChecks();
        $context->setSerializeNull(true);

        $response = new Response(
            $this->serializer->serialize($result, 'json', $context),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/json'
            ]
        );

        $event->setResponse($response);
    }
}