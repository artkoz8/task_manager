<?php

namespace App\Domain\Aggregate;

use App\Domain\Event\CreatedEvent;
use App\Domain\Event\DescriptionChangedEvent;
use App\Domain\Event\EventInterface;
use App\Domain\Event\EventProviderInterface;
use App\Domain\Event\StatusChangedEvent;
use App\Domain\Event\TitleChangedEvent;
use App\Domain\Strategy\TaskStatusStrategyResolver;
use App\Domain\ValueObject\TaskId;
use App\Domain\ValueObject\TaskStatus;
use App\Domain\ValueObject\UserId;
use DomainException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class Task implements EventProviderInterface
{
    private TaskId $id;
    private UserId $authorId;
    private string $title;
    private string $description;
    private TaskStatus $status;

    /** @var EventInterface[] */
    private array $uncommittedEvents = [];

    private function __construct() {}

    public static function create(
        TaskId $id,
        UserId $authorId,
        string $title,
        string $description
    ): self
    {
        $that = new self();

        $that->recordThat(new CreatedEvent(
            id: $id->toString(),
            authorId: $authorId->toString(),
            title: $title,
            description: $description,
            status: TaskStatus::PENDING->value,
        ));

        return $that;
    }

    /**
     * UWAGA ARCHITEKTONICZNA:
     * Na obecnym etapie rekonstytucja odbywa się wyłącznie poprzez pełne odtworzenie
     * strumienia zdarzeń. Brak mechanizmu migawek (snapshots) jest świadomą decyzją projektową,
     * a nie przeoczeniem. Wraz ze wzrostem długości strumieni zdarzeń w przyszłości,
     * implementacja migawek może stać się niezbędna dla optymalizacji wydajności.
     */
    public static function reconstituteFromHistory(iterable $events): self
    {
        $self = new self();
        foreach ($events as $event) {
            $self->apply($event);
        }
        return $self;
    }

    public function changeTitle(string $newTitle): void
    {
        if ($this->title === $newTitle) {
            return;
        }

        $this->recordThat(new TitleChangedEvent(
            id: $this->id->toString(),
            title: $newTitle
        ));
    }

    public function changeStatus(TaskStatus $newStatus, TaskStatusStrategyResolver $strategyResolver): void
    {
        if ($this->status === $newStatus) {
            return;
        }

        $strategy = $strategyResolver->resolve($newStatus);

        if (!$strategy->canTransition($this->status, $newStatus)) {
            throw new DomainException(sprintf(
                "Niedozwolone przejście ze statusu %s do %s.",
                $this->status->value,
                $newStatus->value
            ));
        }

        $this->recordThat(new StatusChangedEvent(
            id: $this->id->toString(),
            status: $newStatus->value
        ));
    }

    public function changeDescription(string $newDescription): void
    {
        if ($this->description === $newDescription) {
            return;
        }

        $this->recordThat(new DescriptionChangedEvent(
            id: $this->id->toString(),
            description: $newDescription
        ));
    }

    /**
     * @return EventInterface[]
     */
    public function getUncommittedEvents(): array
    {
        return $this->uncommittedEvents;
    }

    public function clearUncommittedEvents(): void
    {
        $this->uncommittedEvents = [];
    }

    private function recordThat(EventInterface $event): void
    {
        $this->uncommittedEvents[] = $event;
        $this->apply($event);
    }

    private function apply(object $event): void
    {
        match (true) {
            $event instanceof CreatedEvent => $this->applyTaskCreated($event),
            $event instanceof TitleChangedEvent => $this->applyTaskTitleChanged($event),
            $event instanceof DescriptionChangedEvent => $this->applyTaskDescriptionChanged($event),
            $event instanceof StatusChangedEvent => $this->applyTaskStatusChanged($event),
            default => null
        };
    }

    private function applyTaskCreated(CreatedEvent $event): void
    {
        $this->id = TaskId::fromString($event->id);
        $this->authorId = UserId::fromString($event->authorId);
        $this->title = $event->title;
        $this->description = $event->description;
        $this->status = TaskStatus::from($event->status);
    }

    private function applyTaskTitleChanged(TitleChangedEvent $event): void
    {
        $this->title = $event->title;
    }

    private function applyTaskDescriptionChanged(DescriptionChangedEvent $event): void
    {
        $this->description = $event->description;
    }

    private function applyTaskStatusChanged(StatusChangedEvent $event): void
    {
        $this->status = TaskStatus::from($event->status);
    }

    public function getId(): TaskId
    {
        return $this->id;
    }

    public function getAuthorId(): UserId
    {
        return $this->authorId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }
}
