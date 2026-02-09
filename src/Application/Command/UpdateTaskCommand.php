<?php

namespace App\Application\Command;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class UpdateTaskCommand
{
    public function __construct(
        public string $taskId,
        public ?string $title,
        public ?string $description,
    )
    {
    }
}
