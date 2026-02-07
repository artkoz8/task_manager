<?php

namespace App\Application\FilterCriteria;

use App\Application\Query\GetUsersQuery;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class UserFilterCriteria
{
    private function __construct(
        public ?int $id = null,
        public ?string $username = null,
        public ?string $email = null,
    ) {}

    public static function fromQuery(GetUsersQuery $query): self
    {
        return new self(
            id: $query->id,
            username: $query->username,
            email: $query->email
        );
    }

    public static function createWithEmail(string $email): self
    {
        return new self(email: $email);
    }
}
