<?php

namespace App\Application\Repository;

use App\Application\ReadModel\TaskReadModel;

interface TaskReadModelRepositoryInterface
{
    /**
     * @return TaskReadModel[]
     */
    public function findByAuthorId(string $authorId): array;

    public function findByTaskId(string $taskId): ?TaskReadModel;
}
