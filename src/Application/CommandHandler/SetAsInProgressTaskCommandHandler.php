<?php

namespace App\Application\CommandHandler;

use App\Application\Command\SetAsInProgressTaskCommand;
use App\Application\Common\EventDispatchingHandlerTrait;
use App\Application\ReadModel\TaskReadModel;
use App\Application\Repository\TaskProjectionRepositoryInterface;
use App\Domain\Strategy\TaskStatusStrategyResolver;
use App\Domain\ValueObject\TaskStatus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class SetAsInProgressTaskCommandHandler
{
    use EventDispatchingHandlerTrait;

    public function __construct(
        MessageBusInterface                       $eventBus,
        private TaskProjectionRepositoryInterface $taskRepository,
        private TaskStatusStrategyResolver        $taskStatusStrategyResolver,
    )
    {
        $this->eventBus = $eventBus;
    }

    public function __invoke(SetAsInProgressTaskCommand $command): ?TaskReadModel
    {
        $task = $this->taskRepository->getByTaskId($command->taskId);

        $task->changeStatus(TaskStatus::IN_PROGRESS, $this->taskStatusStrategyResolver);
        $this->dispatchEvents($task);

        return TaskReadModel::fromTask($task);
    }
}
