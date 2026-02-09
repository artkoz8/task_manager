<?php

namespace App\Application\Query;

use App\Infrastructure\GraphQL\Input\UserFiltersInput;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class GetUsersQuery
{
    public function __construct(
        public ?int $id = null,
        public ?string $username = null,
        public ?string $email = null,
    ) {
    }

    public static function fromInput(?UserFiltersInput $input): self
    {
        if (null === $input) {
            return new self();
        }

        return new self(
            id: $input->id,
            username: $input->username,
            email: $input->email
        );
    }
}
