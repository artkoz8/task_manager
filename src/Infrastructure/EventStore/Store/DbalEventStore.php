<?php

namespace App\Infrastructure\EventStore\Store;

use App\Domain\Event\EventInterface;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class DbalEventStore implements EventStoreInterface
{
    private const TABLE_NAME = 'events_store';

    public function __construct(
        private Connection $connection,
        private SerializerInterface $serializer
    ) {
    }

    public function append(EventInterface $event): void
    {
        $this->connection->insert(self::TABLE_NAME, [
            'stream_id' => $event->aggregateId(),
            'event_name' => get_class($event),
            'payload' => $this->serializer->serialize($event, 'json'),
            'recorded_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s.u'),
        ]);
    }

    /**
     * @param string $streamId
     * @return iterable<EventInterface>
     */
    public function getHistory(string $streamId): iterable
    {
        $query = sprintf(
            'SELECT event_name, payload FROM %s WHERE stream_id = :streamId ORDER BY id ASC',
            self::TABLE_NAME
        );

        $result = $this->connection->executeQuery($query, [
            'streamId' => $streamId
        ]);

        while ($row = $result->fetchAssociative()) {
            yield $this->serializer->deserialize(
                $row['payload'],
                $row['event_name'],
                'json'
            );
        }
    }
}
