<?php

namespace App\Application\FilterCriteria;

use App\Application\Query\GetUsersQuery;

final readonly class UserFilterCriteria
{
    public function __construct(
        public ?int $id = null,
        public ?string $username = null,
        public ?string $email = null,
        public ?string $name = null,
    ) {}

    public static function fromQuery(GetUsersQuery $query): self
    {
        return new self(
            id: $query->id,
            username: $query->username,
            email: $query->email
        );
    }

    public function hasFilters(): bool
    {
        return $this->id !== null
            || $this->username !== null
            || $this->email !== null;
    }
}
