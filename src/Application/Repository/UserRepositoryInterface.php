<?php

namespace App\Application\Repository;

use App\Application\FilterCriteria\UserFilterCriteria;
use App\Domain\Entity\User;

interface UserRepositoryInterface
{
    /**
     * @return User[]
     */
    public function findByCriteria(UserFilterCriteria $criteria): array;

    public function findOneById(int $id): ?User;

    public function findOneByEmail(string $email): ?User;
}
