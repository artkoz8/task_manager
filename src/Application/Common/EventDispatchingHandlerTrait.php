<?php

namespace App\Application\Common;

use App\Domain\Event\EventProviderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

trait EventDispatchingHandlerTrait
{
    private MessageBusInterface $eventBus;

    private function dispatchEvents(EventProviderInterface $aggregate): void
    {
        $events = $aggregate->getUncommittedEvents();

        if (empty($events)) {
            return;
        }

        foreach ($events as $event) {
            $this->eventBus->dispatch($event);
        }

        $aggregate->clearUncommittedEvents();
    }
}
