<?php

namespace App\Application\Repository;

use App\Domain\Aggregate\Task;

interface TaskProjectionRepositoryInterface
{
    public function getByTaskId(string $taskId): Task;
}
