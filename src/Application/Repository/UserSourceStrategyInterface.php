<?php

namespace App\Application\Repository;

use App\Application\FilterCriteria\UserFilterCriteria;
use App\Domain\Entity\User;

interface UserSourceStrategyInterface
{
    /** @return User[] */
    public function findByCriteria(UserFilterCriteria $criteria): array;

    public function supports(string $sourceType): bool;
}
