<?php

namespace App\Tests\Domain\Aggregate;

use App\Domain\Aggregate\Task;
use App\Domain\Event\CreatedEvent;
use App\Domain\Strategy\TaskStatusStrategyResolver;
use App\Domain\Strategy\TransitionToCancelledStrategy;
use App\Domain\Strategy\TransitionToCompletedStrategy;
use App\Domain\Strategy\TransitionToInProgressStrategy;
use App\Domain\Strategy\TransitionToPendingStrategy;
use App\Domain\ValueObject\TaskId;
use App\Domain\ValueObject\TaskStatus;
use App\Domain\ValueObject\UserId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TaskStatusTest extends TestCase
{
    private TaskStatusStrategyResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new TaskStatusStrategyResolver([
            new TransitionToPendingStrategy(),
            new TransitionToInProgressStrategy(),
            new TransitionToCompletedStrategy(),
            new TransitionToCancelledStrategy(),
        ]);
    }

    #[Test]
    #[DataProvider('statusTransitionProvider')]
    public function it_should_verify_all_business_rules_for_transitions(
        TaskStatus $current,
        TaskStatus $target,
        bool $shouldBeAllowed
    ): void
    {
        $strategy = $this->resolver->resolve($target);

        $isAllowed = $strategy->canTransition($current, $target);

        $this->assertEquals(
            $shouldBeAllowed,
            $isAllowed,
            sprintf("Transition from %s to %s should be %s", $current->value, $target->value, $shouldBeAllowed ? 'allowed' : 'denied')
        );
    }

    public static function statusTransitionProvider(): iterable
    {
        // Reguła 1: Zawsze do PENDING
        yield 'Any -> PENDING (from In Progress)' => [TaskStatus::IN_PROGRESS, TaskStatus::PENDING, true];
        yield 'Any -> PENDING (from Completed)' => [TaskStatus::COMPLETED, TaskStatus::PENDING, true];
        yield 'Any -> PENDING (from Cancelled)' => [TaskStatus::CANCELLED, TaskStatus::PENDING, true];

        // Reguła 2: Z PENDING można wszędzie
        yield 'PENDING -> IN_PROGRESS' => [TaskStatus::PENDING, TaskStatus::IN_PROGRESS, true];
        yield 'PENDING -> COMPLETED' => [TaskStatus::PENDING, TaskStatus::COMPLETED, true];
        yield 'PENDING -> CANCELLED' => [TaskStatus::PENDING, TaskStatus::CANCELLED, true];

        // Reguła 3: Z CANCELLED można przywrócić
        yield 'CANCELLED -> IN_PROGRESS' => [TaskStatus::CANCELLED, TaskStatus::IN_PROGRESS, true];
        yield 'CANCELLED -> PENDING' => [TaskStatus::CANCELLED, TaskStatus::PENDING, true];

        // Reguła 4: Z IN_PROGRESS do finalnych
        yield 'IN_PROGRESS -> COMPLETED' => [TaskStatus::IN_PROGRESS, TaskStatus::COMPLETED, true];
        yield 'IN_PROGRESS -> CANCELLED' => [TaskStatus::IN_PROGRESS, TaskStatus::CANCELLED, true];

        // Reguła 5: Z COMPLETED tylko do PENDING (Blokada CANCELLED i IN_PROGRESS)
        yield 'COMPLETED -> CANCELLED (Forbidden)' => [TaskStatus::COMPLETED, TaskStatus::CANCELLED, false];
        yield 'COMPLETED -> IN_PROGRESS (Forbidden)' => [TaskStatus::COMPLETED, TaskStatus::IN_PROGRESS, false];
    }

    #[Test]
    #[DataProvider('forbiddenTransitionsProvider')]
    public function it_should_throw_domain_exception_on_all_forbidden_transitions(
        TaskStatus $current,
        TaskStatus $target
    ): void {
        $task = Task::reconstituteFromHistory([
            new CreatedEvent(
                TaskId::generate()->toString(),
                UserId::fromString('user-1')->toString(),
                'Title',
                'Desc',
                $current->value
            )
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(sprintf("Niedozwolone przejście ze statusu %s do %s.", $current->value, $target->value));

        $task->changeStatus($target, $this->resolver);
    }

    public static function forbiddenTransitionsProvider(): iterable
    {
        // Blokady dla statusu COMPLETED (można wyjść tylko do PENDING)
        yield 'COMPLETED -> CANCELLED' => [TaskStatus::COMPLETED, TaskStatus::CANCELLED];
        yield 'COMPLETED -> IN_PROGRESS' => [TaskStatus::COMPLETED, TaskStatus::IN_PROGRESS];

        // Blokady dla statusu CANCELLED
        yield 'CANCELLED -> COMPLETED' => [TaskStatus::CANCELLED, TaskStatus::COMPLETED];
    }
}
