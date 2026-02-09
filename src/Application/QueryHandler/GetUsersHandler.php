<?php

namespace App\Application\QueryHandler;

use App\Application\FilterCriteria\UserFilterCriteria;
use App\Application\Query\GetUsersQuery;
use App\Application\ReadModel\UserReadModel;
use App\Application\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
readonly class GetUsersHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {
    }

    /**
     * @return UserReadModel[]
     */
    public function __invoke(GetUsersQuery $query): array
    {
        $criteria = UserFilterCriteria::create()
            ->withId($query->id)
            ->withUsername($query->username)
            ->withEmail($query->email);

        $users = $this->userRepository->findByCriteria($criteria);

        return array_map(
            fn($user) => UserReadModel::fromEntity($user),
            $users
        );
    }
}
