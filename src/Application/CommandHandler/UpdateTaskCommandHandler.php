<?php

namespace App\Application\CommandHandler;

use App\Application\Command\UpdateTaskCommand;
use App\Application\Common\EventDispatchingHandlerTrait;
use App\Application\ReadModel\TaskReadModel;
use App\Application\Repository\TaskProjectionRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateTaskCommandHandler
{
    use EventDispatchingHandlerTrait;

    public function __construct(
        MessageBusInterface $eventBus,
        private TaskProjectionRepositoryInterface $taskRepository
    )
    {
        $this->eventBus = $eventBus;
    }

    public function __invoke(UpdateTaskCommand $command): ?TaskReadModel
    {
        $task = $this->taskRepository->getByTaskId($command->taskId);

        if (null !== $command->title) {
            $task->changeTitle($command->title);
        }

        if (null !== $command->description) {
            $task->changeDescription($command->description);
        }

        $this->dispatchEvents($task);

        return TaskReadModel::fromTask($task);
    }
}
