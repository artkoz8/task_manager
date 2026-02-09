<?php

namespace App\Infrastructure\GraphQL\Resolver;

use App\Application\Query\GetTaskHistoryQuery;
use App\Application\Query\GetTasksQuery;
use App\Application\ReadModel\TaskHistoryReadModel;
use App\Application\ReadModel\TaskReadModel;
use App\Infrastructure\GraphQL\Type\TaskHistoryEntry;
use App\Infrastructure\GraphQL\Type\TaskPayload;
use App\Infrastructure\Security\User\UserContext;
use Symfony\Component\Messenger\HandleTrait;
use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\Messenger\MessageBusInterface;

#[GQL\Provider]
class TaskQueryResolver
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $queryBus,
        private readonly UserContext $userContext
    )
    {
        $this->messageBus = $queryBus;
    }

    #[GQL\Query(name: "myTasks", type: "[TaskPayload!]")]
    public function getMyTasks(): array
    {
        $user = $this->userContext->getCurrentUser();

        /** @var TaskReadModel[] $tasks */
        $tasks = $this->handle(new GetTasksQuery(authorId: (string) $user->id));

        return array_map(
            fn(TaskReadModel $model) => TaskPayload::createFromReadModel($model),
            $tasks
        );
    }

    #[GQL\Query(name: "taskHistory", type: "[TaskHistoryEntry!]")]
    #[GQL\Arg(name: "taskId", type: "String!")]
    public function getTaskHistory(string $taskId): array
    {
        $this->userContext->getCurrentUser();

        /** @var TaskHistoryReadModel[] $history */
        $history = $this->handle(new GetTaskHistoryQuery($taskId));

        return array_map(
            fn(TaskHistoryReadModel $model) => TaskHistoryEntry::fromTaskHistoryReadModel($model),
            $history
        );
    }
}
