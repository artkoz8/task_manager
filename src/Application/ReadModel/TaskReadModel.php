<?php

namespace App\Application\ReadModel;

use App\Domain\Aggregate\Task;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class TaskReadModel
{
    public function __construct(
        public string $id,
        public string $authorId,
        public string $title,
        public string $description,
        public string $status,
    )
    {
    }

    public static function fromTask(Task $task): self
    {
        return new self(
            $task->getId()->toString(),
            $task->getAuthorId()->toString(),
            $task->getTitle(),
            $task->getDescription(),
            $task->getStatus()->value,
        );
    }
}
