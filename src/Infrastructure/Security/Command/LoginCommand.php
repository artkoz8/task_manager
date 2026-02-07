<?php

namespace App\Infrastructure\Security\Command;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class LoginCommand
{
    private function __construct(
        public string $email
    ) {
    }

    public static function create(string $email): self
    {
        return new self($email);
    }
}
