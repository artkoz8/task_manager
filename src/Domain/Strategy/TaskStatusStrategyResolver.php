<?php

namespace App\Domain\Strategy;

use App\Domain\ValueObject\TaskStatus;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class TaskStatusStrategyResolver
{
    /** @var TaskStatusStrategyInterface[] */
    private array $strategies;

    public function __construct(
        #[AutowireIterator('app.task_status_strategy')]
        iterable $strategies
    )
    {
        $this->strategies = $strategies instanceof \Traversable ? iterator_to_array($strategies) : $strategies;
    }

    public function resolve(TaskStatus $target): TaskStatusStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($target)) {
                return $strategy;
            }
        }

        throw new RuntimeException("Brak zdefiniowanej strategii dla statusu: $target->value");
    }
}
