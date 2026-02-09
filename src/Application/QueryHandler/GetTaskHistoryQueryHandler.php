<?php

namespace App\Application\QueryHandler;

use App\Application\Query\GetTaskHistoryQuery;
use App\Application\ReadModel\TaskHistoryReadModel;
use App\Domain\Event\EventInterface;
use App\Infrastructure\EventStore\Store\EventStoreInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
readonly class GetTaskHistoryQueryHandler
{
    public function __construct(
        private EventStoreInterface $eventStore
    )
    {
    }

    /**
     * @return TaskHistoryReadModel[]
     */
    public function __invoke(GetTaskHistoryQuery $query): array
    {
        /** @var EventInterface[] $eventsHistory */
        $eventsHistory = $this->eventStore->getHistory($query->taskId);
        $events = [];
        $history = [];

        foreach ($eventsHistory as $event) {
            $events[] = $event;;
            $history[] = TaskHistoryReadModel::fromHistoryEvents($events);
        }

        return $history;
    }
}
