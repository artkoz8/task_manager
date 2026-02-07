<?php

namespace App\Infrastructure\Security\User;

use App\Application\ReadModel\UserReadModel;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Security\Core\User\UserInterface;

#[Exclude]
final readonly class AuthUserAdapter implements UserInterface
{
    private function __construct(
        private UserReadModel $user
    ) {
    }

    public static function create(UserReadModel $user): self
    {
        return new self($user);
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->user->id;
    }

    public function getDomainUser(): UserReadModel
    {
        return $this->user;
    }
}
