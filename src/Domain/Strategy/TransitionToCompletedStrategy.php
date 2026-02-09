<?php

namespace App\Domain\Strategy;

use App\Domain\ValueObject\TaskStatus;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.task_status_strategy')]
final readonly class TransitionToCompletedStrategy implements TaskStatusStrategyInterface
{
    public function supports(TaskStatus $target): bool
    {
        return $target === TaskStatus::COMPLETED;
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
