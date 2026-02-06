<?php

namespace App\Application\Repository;

use App\Domain\Entity\User;

interface UserSourceStrategyInterface
{
    /** @return User[] */
    public function fetchAll(): array;

    public function fetchById(int $id): ?User;

    public function fetchByEmail(string $email): ?User;

    public function fetchByUsername(string $username): ?User;

    public function supports(string $sourceType): bool;
}
