<?php

namespace App\Infrastructure\Projection;

use App\Application\ReadModel\TaskReadModel;
use App\Application\ReadModel\TaskReadModelGatewayInterface;
use Doctrine\DBAL\Connection;

final readonly class DbalTaskReadModelGateway implements TaskReadModelGatewayInterface
{
    private const TABLE_NAME = 'tasks_read';

    public function __construct(
        private Connection $connection
    ) {
    }

    public function insert(TaskReadModel $readModel): void
    {
        $this->connection->insert(self::TABLE_NAME, [
            'id' => $readModel->id,
            'author_id' => $readModel->authorId,
            'title' => $readModel->title,
            'description' => $readModel->description,
            'status' => $readModel->status,
            'created_at' => $this->getCurrentTimestamp(),
            'updated_at' => $this->getCurrentTimestamp(),
        ]);
    }

    public function update(TaskReadModel $readModel): void
    {
        $this->connection->update(
            self::TABLE_NAME,
            [
                'title' => $readModel->title,
                'description' => $readModel->description,
                'status' => $readModel->status,
                'updated_at' => $this->getCurrentTimestamp(),
            ],
            ['id' => $readModel->id]
        );
    }

    private function getCurrentTimestamp(): string
    {
        return (new \DateTimeImmutable())->format('Y-m-d H:i:s');
    }
}
