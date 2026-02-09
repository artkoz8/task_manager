<?php

namespace App\Domain\Strategy;

use App\Domain\ValueObject\TaskStatus;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.task_status_strategy')]
final readonly class TransitionToCancelledStrategy implements TaskStatusStrategyInterface
{
    public function supports(TaskStatus $target): bool
    {
        return $target === TaskStatus::CANCELLED;
    }

    public function canTransition(TaskStatus $current, TaskStatus $target): bool
    {
        return in_array(
            $current,
            [TaskStatus::PENDING, TaskStatus::IN_PROGRESS],
            true
        );
    }
}
