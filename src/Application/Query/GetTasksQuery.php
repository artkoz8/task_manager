<?php

namespace App\Application\Query;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class GetTasksQuery
{
    public function __construct(
        public ?string $authorId = null,
    ) {
    }
}
