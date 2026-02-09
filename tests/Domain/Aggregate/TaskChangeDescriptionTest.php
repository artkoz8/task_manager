<?php

namespace App\Tests\Domain\Aggregate;

use App\Domain\Aggregate\Task;
use App\Domain\Event\DescriptionChangedEvent;
use App\Domain\ValueObject\TaskId;
use App\Domain\ValueObject\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TaskChangeDescriptionTest extends TestCase
{
    #[Test]
    public function it_should_change_description(): void
    {
        $task = Task::create(
            TaskId::generate(),
            UserId::fromString('user-1'),
            'Tytuł',
            'Stary Opis'
        );
        $task->clearUncommittedEvents();

        $newDescription = 'Zaktualizowany Opis';

        $task->changeDescription($newDescription);

        $this->assertEquals($newDescription, $task->getDescription());

        $events = $task->getUncommittedEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(DescriptionChangedEvent::class, $events[0]);
        $this->assertEquals($newDescription, $events[0]->description);
    }

    #[Test]
    public function it_should_not_record_event_if_description_is_the_same(): void
    {
        $description = 'Niezmieniony opis';
        $task = Task::create(
            TaskId::generate(),
            UserId::fromString('user-1'),
            'Tytuł',
            $description
        );
        $task->clearUncommittedEvents();

        $task->changeDescription($description);

        $this->assertEmpty($task->getUncommittedEvents());
    }
}
