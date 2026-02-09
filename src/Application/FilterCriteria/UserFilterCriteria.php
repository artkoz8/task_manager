<?php

namespace App\Application\FilterCriteria;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class UserFilterCriteria
{
    private ?int $id = null;
    private ?string $username = null;
    private ?string $email = null;

    public static function create(): self
    {
        return new self();
    }

    public function withId(?int $id): self
    {
        if ($id === null) {
            return $this;
        }

        $that = clone $this;
        $that->id = $id;

        return $that;
    }

    public function withUsername(?string $username): self
    {
        if ($username === null) {
            return $this;
        }

        $that = clone $this;
        $that->username = $username;

        return $that;
    }

    public function withEmail(?string $email): self
    {
        if ($email === null) {
            return $this;
        }

        $that = clone $this;
        $that->email = $email;

        return $that;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
