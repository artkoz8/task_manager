<?php

namespace App\Infrastructure\GraphQL\Resolver;

use App\Application\Command\CreateTaskCommand;
use App\Application\Command\SetAsCanceledTaskCommand;
use App\Application\Command\SetAsCompleteTaskCommand;
use App\Application\Command\SetAsPendingTaskCommand;
use App\Application\Command\SetAsInProgressTaskCommand;
use App\Application\Command\UpdateTaskCommand;
use App\Infrastructure\GraphQL\Input\CreateTaskInput;
use App\Infrastructure\GraphQL\Input\UpdateTaskInput;
use App\Infrastructure\GraphQL\Type\TaskPayload;
use App\Infrastructure\Security\User\UserContext;
use Symfony\Component\Messenger\HandleTrait;
use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\Messenger\MessageBusInterface;

#[GQL\Provider]
class TaskMutationResolver
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $commandBus,
        private readonly UserContext $userContext
    )
    {
        $this->messageBus = $commandBus;
    }

    #[GQL\Mutation(name: "createTask", type: "TaskPayload")]
    #[GQL\Arg(name: 'createTaskInput', type: 'CreateTaskInput!')]
    public function createTask(CreateTaskInput $createTaskInput): TaskPayload
    {
        $authorId = $this->userContext->getCurrentUser()->id;

        $createdTask = $this->handle(new CreateTaskCommand(
            $createTaskInput->title,
            $createTaskInput->description,
            $authorId
        ));

        return TaskPayload::createFromReadModel($createdTask);
    }

    #[GQL\Mutation(name: "updateTask", type: "TaskPayload")]
    #[GQL\Arg(name: 'taskId', type: 'String!')]
    #[GQL\Arg(name: 'updateTaskInput', type: 'UpdateTaskInput!')]
    public function updateTask(string $taskId, UpdateTaskInput $updateTaskInput): TaskPayload
    {
        $this->userContext->getCurrentUser();

        $task = $this->handle(new UpdateTaskCommand(
            $taskId,
            $updateTaskInput->title,
            $updateTaskInput->description
        ));

        return TaskPayload::createFromReadModel($task);
    }

    #[GQL\Mutation(name: "setAsPendingTask", type: "TaskPayload")]
    #[GQL\Arg(name: 'taskId', type: 'String!')]
    public function setAsPendingTask(string $taskId): TaskPayload
    {
        $this->userContext->getCurrentUser();

        $createdTask = $this->handle(new SetAsPendingTaskCommand($taskId));

        return TaskPayload::createFromReadModel($createdTask);
    }

    #[GQL\Mutation(name: "setAsInProgressTask", type: "TaskPayload")]
    #[GQL\Arg(name: 'taskId', type: 'String!')]
    public function setAsInProgressTask(string $taskId): TaskPayload
    {
        $this->userContext->getCurrentUser();

        $task = $this->handle(new SetAsInProgressTaskCommand($taskId));

        return TaskPayload::createFromReadModel($task);
    }

    #[GQL\Mutation(name: "setAsCompleteTask", type: "TaskPayload")]
    #[GQL\Arg(name: 'taskId', type: 'String!')]
    public function setAsCompleteTask(string $taskId): TaskPayload
    {
        $this->userContext->getCurrentUser();

        $task = $this->handle(new SetAsCompleteTaskCommand($taskId));

        return TaskPayload::createFromReadModel($task);
    }

    #[GQL\Mutation(name: "setAsCanceledTask", type: "TaskPayload")]
    #[GQL\Arg(name: 'taskId', type: 'String!')]
    public function setAsCanceledTask(string $taskId): TaskPayload
    {
        $this->userContext->getCurrentUser();

        $task = $this->handle(new SetAsCanceledTaskCommand($taskId));

        return TaskPayload::createFromReadModel($task);
    }
}
