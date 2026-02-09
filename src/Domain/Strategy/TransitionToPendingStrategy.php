<?php

namespace App\Domain\Strategy;

use App\Domain\ValueObject\TaskStatus;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.task_status_strategy')]
final readonly class TransitionToPendingStrategy implements TaskStatusStrategyInterface
{
    public function supports(TaskStatus $target): bool
    {
        return $target === TaskStatus::PENDING;
    }

    public function canTransition(TaskStatus $current, TaskStatus $target): bool
    {
        return true;
    }
}
