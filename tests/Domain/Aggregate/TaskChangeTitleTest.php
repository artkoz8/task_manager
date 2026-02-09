<?php

namespace App\Tests\Domain\Aggregate;

use App\Domain\Aggregate\Task;
use App\Domain\Event\TitleChangedEvent;
use App\Domain\ValueObject\TaskId;
use App\Domain\ValueObject\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TaskChangeTitleTest extends TestCase
{
    #[Test]
    public function it_should_change_title(): void
    {
        $task = Task::create(
            TaskId::generate(),
            UserId::fromString('user-1'),
            'Stary Tytuł',
            'Opis'
        );
        $task->clearUncommittedEvents();

        $newTitle = 'Nowy Tytuł';

        $task->changeTitle($newTitle);

        $this->assertEquals($newTitle, $task->getTitle());

        $events = $task->getUncommittedEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(TitleChangedEvent::class, $events[0]);
        $this->assertEquals($newTitle, $events[0]->title);
    }

    #[Test]
    public function it_should_not_record_event_if_title_is_the_same(): void
    {
        $title = 'Ten Sam Tytuł';
        $task = Task::create(
            TaskId::generate(),
            UserId::fromString('user-1'),
            $title,
            'Opis'
        );
        $task->clearUncommittedEvents();

        $task->changeTitle($title);

        $this->assertEmpty($task->getUncommittedEvents());
    }
}
