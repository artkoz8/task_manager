<?php

namespace App\Application\Projection;

use App\Application\ReadModel\TaskReadModel;
use App\Application\ReadModel\TaskReadModelGatewayInterface;
use App\Application\Repository\TaskProjectionRepositoryInterface;
use App\Domain\Event\DescriptionChangedEvent;
use App\Domain\Event\EventInterface;
use App\Domain\Event\StatusChangedEvent;
use App\Domain\Event\CreatedEvent;
use App\Domain\Event\TitleChangedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus', priority: -10)]
final readonly class TaskReadModelProjector
{

    public function __construct(
        private TaskReadModelGatewayInterface     $gateway,
        private TaskProjectionRepositoryInterface $taskRepository,
    ) {
    }

    public function __invoke(EventInterface $event): void
    {
        match (true) {
            $event instanceof CreatedEvent => $this->handleCreated($event),

            $event instanceof StatusChangedEvent,
                $event instanceof TitleChangedEvent,
                $event instanceof DescriptionChangedEvent => $this->handleUpdate($event->id),

            default => null
        };
    }

    public function handleCreated(CreatedEvent $event): void
    {
        $readModel = new TaskReadModel(
            $event->id,
            $event->authorId,
            $event->title,
            $event->description,
            $event->status
        );

        $this->gateway->insert($readModel);
    }
    private function handleUpdate(string $taskId): void
    {
        $task = $this->taskRepository->getByTaskId($taskId);
        $readModel = TaskReadModel::fromTask($task);

        $this->gateway->update($readModel);
    }
}
