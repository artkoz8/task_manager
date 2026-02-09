<?php

namespace App\Domain\Strategy;

use App\Domain\ValueObject\TaskStatus;

interface TaskStatusStrategyInterface
{
    public function supports(TaskStatus $target): bool;

    public function canTransition(TaskStatus $current, TaskStatus $target): bool;
}
