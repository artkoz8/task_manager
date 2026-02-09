<?php

namespace App\Infrastructure\Repository;

use App\Application\ReadModel\TaskReadModel;
use App\Application\Repository\TaskReadModelRepositoryInterface;
use Doctrine\DBAL\Connection;

final readonly class DbalTaskReadModelRepository implements TaskReadModelRepositoryInterface
{
    private const TABLE_NAME = 'tasks_read';

    public function __construct(private Connection $connection) {}

    public function findByAuthorId(string $authorId): array
    {
        $query = sprintf('SELECT * FROM %s WHERE author_id = :authorId ORDER BY created_at DESC', self::TABLE_NAME);
        $rows = $this->connection->fetchAllAssociative($query, ['authorId' => $authorId]);

        return array_map(fn(array $row) => $this->mapToReadModel($row), $rows);
    }

    public function findByTaskId(string $taskId): ?TaskReadModel
    {
        $query = sprintf('SELECT * FROM %s WHERE id = :id', self::TABLE_NAME);
        $row = $this->connection->fetchAssociative($query, ['id' => $taskId]);

        return $row ? $this->mapToReadModel($row) : null;
    }

    private function mapToReadModel(array $row): TaskReadModel
    {
        return new TaskReadModel(
            (string) $row['id'],
            (string) $row['author_id'],
            (string) $row['title'],
            (string) $row['description'],
            (string) $row['status']
        );
    }
}
