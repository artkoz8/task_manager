<?php

namespace App\Domain\Event;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class StatusChangedEvent implements EventInterface
{
    public function __construct(
        public string $id,
        public string $status,
    )
    {
    }

    public function aggregateId(): string
    {
        return $this->id;
    }
}
