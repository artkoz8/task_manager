<?php

namespace App\Application\Command;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class CreateTaskCommand
{
    public function __construct(
        public string $title,
        public string $description,
        public string $authorId,
    )
    {
    }
}
