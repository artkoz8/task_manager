<?php

namespace App\Tests\Domain\Aggregate;

use App\Domain\Aggregate\Task;
use App\Domain\Event\CreatedEvent;
use App\Domain\ValueObject\TaskId;
use App\Domain\ValueObject\TaskStatus;
use App\Domain\ValueObject\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TaskCreateTest extends TestCase
{
    #[Test]
    public function it_should_create_new_task_with_correct_initial_state(): void
    {
        $id = TaskId::generate();
        $authorId = UserId::fromString('user-123');
        $title = 'Zadanie testowe';
        $description = 'Opis zadania';

        $task = Task::create($id, $authorId, $title, $description);

        $this->assertEquals($id, $task->getId());
        $this->assertEquals($authorId, $task->getAuthorId());
        $this->assertEquals($title, $task->getTitle());
        $this->assertEquals($description, $task->getDescription());
        $this->assertEquals(TaskStatus::PENDING, $task->getStatus());
    }

    #[Test]
    public function it_should_record_task_created_event(): void
    {
        $id = TaskId::generate();
        $authorId = UserId::fromString('user-123');
        $title = 'Zadanie ze zdarzeniem';
        $description = 'Opis';

        $task = Task::create($id, $authorId, $title, $description);

        $events = $task->getUncommittedEvents();
        $this->assertCount(1, $events);

        /** @var CreatedEvent $event */
        $event = $events[0];
        $this->assertInstanceOf(CreatedEvent::class, $event);
        $this->assertEquals($id->toString(), $event->id);
        $this->assertEquals($authorId->toString(), $event->authorId);
        $this->assertEquals($title, $event->title);
        $this->assertEquals(TaskStatus::PENDING->value, $event->status);
    }
}
