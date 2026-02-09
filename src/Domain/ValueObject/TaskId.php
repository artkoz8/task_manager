<?php

namespace App\Domain\ValueObject;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Uid\Uuid;

#[Exclude]
final readonly class TaskId
{
    private function __construct(private string $value) {}

    public static function fromString(string $value): self
    {
        if (!Uuid::isValid($value)) {
            throw new InvalidArgumentException("Invalid UUID for TaskId");
        }
        return new self($value);
    }

    public static function generate(): self
    {
        return new self(Uuid::v4()->toString());
    }

    public function toString(): string
    {
        return $this->value;
    }
}
