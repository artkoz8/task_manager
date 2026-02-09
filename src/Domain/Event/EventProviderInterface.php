<?php

namespace App\Domain\Event;

interface EventProviderInterface
{
    /**
     * @return EventInterface[]
     */
    public function getUncommittedEvents(): array;

    public function clearUncommittedEvents(): void;
}
