<?php

namespace App\Application\ReadModel;

use App\Domain\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
readonly class UserReadModel
{
    private function __construct(
        public int $id,
        public string $name,
        public string $username,
        public string $email
    ) {
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            $user->getId(),
            $user->getName(),
            $user->getUsername(),
            $user->getEmail()
        );
    }
}
