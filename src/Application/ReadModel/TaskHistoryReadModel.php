<?php

namespace App\Application\ReadModel;

use App\Domain\Aggregate\Task;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class TaskHistoryReadModel
{
    public function __construct(
        public TaskReadModel $task,
        public string $eventName,
    )
    {
    }

    public static function fromHistoryEvents(array $events): self
    {
        $task = Task::reconstituteFromHistory($events);
        return new TaskHistoryReadModel(
            task: TaskReadModel::fromTask($task),
            eventName: get_class(array_last($events))
        );
    }
}
