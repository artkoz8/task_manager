<?php

namespace App\Infrastructure\Security\Jwt;

use App\Application\Security\Jwt\JwtFactoryInterface;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class LcobucciJwtFactory implements JwtFactoryInterface
{
    public function __construct(
        private readonly Configuration $configuration,
        #[Autowire(env: 'JWT_ISSUER')]
        private readonly string $issuer,
        #[Autowire(env: 'JWT_AUDIENCE')]
        private readonly string $audience,
        #[Autowire(env: 'int:JWT_TTL')]
        private readonly int $tokenTtl = 3600
    ) {
    }

    public function createToken(string $userId, string $email): string
    {
        $now = new DateTimeImmutable();

        $token = $this->configuration->builder()
            ->issuedBy($this->issuer)
            ->permittedFor($this->audience)
            ->identifiedBy(bin2hex(random_bytes(16)))
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify(sprintf('+%d seconds', $this->tokenTtl)))
            ->relatedTo($userId)
            ->withClaim('email', $email)
            ->getToken($this->configuration->signer(), $this->configuration->signingKey());

        return $token->toString();
    }
}
