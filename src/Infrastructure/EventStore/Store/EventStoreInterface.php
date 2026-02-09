<?php

namespace App\Infrastructure\EventStore\Store;

use App\Domain\Event\EventInterface;

interface EventStoreInterface
{
    public function append(EventInterface $event): void;

    public function getHistory(string $streamId): iterable;
}
