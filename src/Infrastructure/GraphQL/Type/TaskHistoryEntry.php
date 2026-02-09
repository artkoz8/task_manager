<?php

namespace App\Infrastructure\GraphQL\Type;

use App\Application\ReadModel\TaskHistoryReadModel;
use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
#[GQL\Type(name: "TaskHistoryEntry")]
final readonly class TaskHistoryEntry
{
    private function __construct(
        #[GQL\Field(type: "TaskPayload!")]
        public TaskPayload $task,

        #[GQL\Field(type: "String!")]
        public string $eventName,
    )
    {
    }

    public static function fromTaskHistoryReadModel(TaskHistoryReadModel $taskHistoryReadModel): self
    {
        return new self(
            task: TaskPayload::createFromReadModel($taskHistoryReadModel->task),
            eventName: $taskHistoryReadModel->eventName
        );
    }
}
