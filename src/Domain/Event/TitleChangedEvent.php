<?php

namespace App\Domain\Event;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class TitleChangedEvent implements EventInterface
{
    public function __construct(
        public string $id,
        public string $title
    )
    {
    }

    public function aggregateId(): string
    {
        return $this->id;
    }
}
