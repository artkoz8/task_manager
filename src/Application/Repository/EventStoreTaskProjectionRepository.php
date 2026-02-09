<?php

namespace App\Application\Repository;

use App\Domain\Aggregate\Task;
use App\Infrastructure\EventStore\Store\EventStoreInterface;
use DomainException;

final readonly class EventStoreTaskProjectionRepository implements TaskProjectionRepositoryInterface
{
    public function __construct(
        private EventStoreInterface $eventStore
    ) {
    }

    public function getByTaskId(string $taskId): Task
    {
        $task = $this->get($taskId);

        if ($task === null) {
            throw new DomainException(sprintf("Zadanie o ID %s nie zostało znalezione.", $taskId));
        }

        return $task;
    }

    /**
     * Pobiera agregat po ID, odtwarzając go z historii zdarzeń.
     */
    private function get(string $taskId): ?Task
    {
        $events = iterator_to_array($this->eventStore->getHistory($taskId));

        if (empty($events)) {
            return null;
        }

        return Task::reconstituteFromHistory($events);
    }
}
