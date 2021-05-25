<?php


namespace App\Modules\Shared\Infrastructure;

use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

interface EventHandler extends MessageSubscriberInterface
{
}