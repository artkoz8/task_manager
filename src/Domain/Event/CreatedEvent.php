<?php

namespace App\Domain\Event;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class CreatedEvent implements EventInterface
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

    public function aggregateId(): string
    {
        return $this->id;
    }
}
