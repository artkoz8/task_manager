<?php

namespace App\Domain\Entity;

readonly class User
{
    private function __construct(
        private int $id,
        private string $name,
        private string $username,
        private string $email
    ) {
    }

    public static function create(
        int $id,
        string $name,
        string $username,
        string $email
    ): self
    {
        return new self($id, $name, $username, $email);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
