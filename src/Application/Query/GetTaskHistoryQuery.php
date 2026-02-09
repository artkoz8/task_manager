<?php

namespace App\Application\Query;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class GetTaskHistoryQuery
{
    public function __construct(
        public string $taskId,
    ) {
    }
}
