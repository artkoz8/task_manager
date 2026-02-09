<?php

namespace App\Domain\ValueObject;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final readonly class UserId
{
    private function __construct(private string $value) {}

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
