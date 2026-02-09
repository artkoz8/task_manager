<?php

namespace App\Application\QueryHandler;

use App\Application\FilterCriteria\UserFilterCriteria;
use App\Application\Query\GetTasksQuery;
use App\Application\Query\GetUsersQuery;
use App\Application\ReadModel\TaskReadModel;
use App\Application\ReadModel\UserReadModel;
use App\Application\Repository\TaskReadModelRepositoryInterface;
use App\Application\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
readonly class GetTasksQueryHandler
{
    public function __construct(
        private TaskReadModelRepositoryInterface $taskReadModelRepository
    )
    {
    }

    /**
     * @return TaskReadModel[]
     */
    public function __invoke(GetTasksQuery $query): array
    {
        return $this->taskReadModelRepository->findByAuthorId($query->authorId);
    }
}
