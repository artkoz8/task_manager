<?php

namespace App\Tests\Domain\Aggregate;

use App\Domain\Aggregate\Task;
use App\Domain\Event\CreatedEvent;
use App\Domain\Event\DescriptionChangedEvent;
use App\Domain\Event\TitleChangedEvent;
use App\Domain\ValueObject\TaskStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TaskReconstituteTest extends TestCase
{
    /**
     * UWAGA ARCHITEKTONICZNA:
     * Na obecnym etapie rekonstytucja odbywa się wyłącznie poprzez pełne odtworzenie
     * strumienia zdarzeń. Brak mechanizmu migawek (snapshots) jest świadomą decyzją projektową,
     * a nie przeoczeniem. Wraz ze wzrostem długości strumieni zdarzeń w przyszłości,
     * implementacja migawek może stać się niezbędna dla optymalizacji wydajności.
     */
    #[Test]
    #[DataProvider('historyProvider')]
    public function it_should_reconstitute_from_history(
        iterable $events,
        string $expectedAuthorId,
        string $expectedTitle,
        string $expectedDescription,
        TaskStatus $expectedStatus
    ): void
    {
        $task = Task::reconstituteFromHistory($events);

        $this->assertEquals($expectedAuthorId, $task->getAuthorId()->toString());
        $this->assertEquals($expectedTitle, $task->getTitle());
        $this->assertEquals($expectedDescription, $task->getDescription());
        $this->assertEquals($expectedStatus, $task->getStatus());
        $this->assertEmpty($task->getUncommittedEvents(), 'Reconstituted aggregate should not have uncommitted events');
    }

    public static function historyProvider(): iterable
    {
        yield 'simple created task' => [
            'events' => [
                new CreatedEvent(
                    id: '550e8400-e29b-41d4-a716-446655440000',
                    authorId: 'user-1',
                    title: 'Historyczny tytuł',
                    description: 'Opis',
                    status: TaskStatus::PENDING->value
                )
            ],
            'expectedAuthorId' => 'user-1',
            'expectedTitle' => 'Historyczny tytuł',
            'expectedDescription' => 'Opis',
            'expectedStatus' => TaskStatus::PENDING
        ];

        yield 'task with changed title' => [
            'events' => [
                new CreatedEvent(
                    id: '550e8400-e29b-41d4-a716-446655440001',
                    authorId: 'user-1',
                    title: 'Pierwotny tytuł',
                    description: 'Opis',
                    status: TaskStatus::PENDING->value
                ),
                new TitleChangedEvent(
                    id: '550e8400-e29b-41d4-a716-446655440001',
                    title: 'Zmieniony tytuł po rekonstytucji'
                )
            ],
            'expectedAuthorId' => 'user-1',
            'expectedTitle' => 'Zmieniony tytuł po rekonstytucji',
            'expectedDescription' => 'Opis',
            'expectedStatus' => TaskStatus::PENDING
        ];

        yield 'task with changed description' => [
            'events' => [
                new CreatedEvent(
                    id: '550e8400-e29b-41d4-a716-446655440002',
                    authorId: 'user-1',
                    title: 'Tytuł',
                    description: 'Pierwotny opis',
                    status: TaskStatus::PENDING->value
                ),
                new DescriptionChangedEvent(
                    id: '550e8400-e29b-41d4-a716-446655440002',
                    description: 'Zmieniony opis po rekonstytucji'
                )
            ],
            'expectedAuthorId' => 'user-1',
            'expectedTitle' => 'Tytuł',
            'expectedDescription' => 'Zmieniony opis po rekonstytucji',
            'expectedStatus' => TaskStatus::PENDING
        ];

        // Tutaj w przyszłości można dodać kolejne zestawy zdarzeń (np. Created + Updated)
    }
}
