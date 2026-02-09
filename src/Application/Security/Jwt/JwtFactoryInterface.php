<?php

namespace App\Application\Security\Jwt;

interface JwtFactoryInterface
{
    public function createToken(string $userId, string $email): string;
}
