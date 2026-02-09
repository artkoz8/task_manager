<?php

namespace App\Application\CommandHandler;

use App\Application\Command\CreateTaskCommand;
use App\Application\Common\EventDispatchingHandlerTrait;
use App\Application\ReadModel\TaskReadModel;
use App\Domain\Aggregate\Task;
use App\Domain\ValueObject\TaskId;
use App\Domain\ValueObject\UserId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
class CreateTaskCommandHandler
{
    use EventDispatchingHandlerTrait;

    public function __construct(
        MessageBusInterface $eventBus,
    )
    {
        $this->eventBus = $eventBus;
    }

    public function __invoke(CreateTaskCommand $command): TaskReadModel
    {
        $taskId = TaskId::generate();

        $task = Task::create(
            id: $taskId,
            authorId: UserId::fromString($command->authorId),
            title: $command->title,
            description: $command->description,
        );

        $this->dispatchEvents($task);

        return TaskReadModel::fromTask($task);
    }
}
