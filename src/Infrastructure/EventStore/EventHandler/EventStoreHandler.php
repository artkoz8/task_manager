<?php

namespace App\Infrastructure\EventStore\EventHandler;

use App\Domain\Event\EventInterface;
use App\Infrastructure\EventStore\Store\EventStoreInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus', priority: 100)]
final readonly class EventStoreHandler
{
    public function __construct(
        private EventStoreInterface $eventStore
    )
    {
    }

    public function __invoke(EventInterface $event): void
    {
        $this->eventStore->append($event);
    }
}
