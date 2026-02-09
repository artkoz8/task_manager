<?php

namespace App\Domain\Event;

interface EventInterface
{
    public function aggregateId(): string;
}
