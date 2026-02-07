<?php

namespace App\Infrastructure\Security\User;

use App\Application\ReadModel\UserReadModel;
use RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;

class UserContext
{
    public function __construct(
        private Security $security
    ) {}

    public function getUser(): ?UserReadModel
    {
        $user = $this->security->getUser();

        if ($user instanceof AuthUserAdapter) {
            return $user->getDomainUser();
        }

        return null;
    }

    public function getCurrentUser(): UserReadModel
    {
        $user = $this->security->getUser();

        if (!$user instanceof AuthUserAdapter) {
            throw new RuntimeException('Użytkownik nie jest zalogowany.');
        }

        return $user->getDomainUser();
    }

    public function hasUser(): bool
    {
        return $this->security->getUser() instanceof AuthUserAdapter;
    }
}
