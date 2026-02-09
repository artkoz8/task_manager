<?php

namespace App\Application\Command;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class SetAsCanceledTaskCommand
{
    public function __construct(public string $taskId)
    {
    }
}
