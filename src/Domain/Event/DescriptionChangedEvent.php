<?php

namespace App\Domain\Event;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class DescriptionChangedEvent implements EventInterface
{
    public function __construct(
        public string $id,
        public string $description
    )
    {
    }

    public function aggregateId(): string
    {
        return $this->id;
    }
}
