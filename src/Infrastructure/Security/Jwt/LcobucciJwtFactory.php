<?php

namespace App\Infrastructure\Security\Jwt;

use App\Application\Security\Jwt\JwtFactoryInterface;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class LcobucciJwtFactory implements JwtFactoryInterface
{
    private Configuration $configuration;

    public function __construct(
        #[Autowire(param: 'jwt_private_key')]
        string $privateKeyPath,
        #[Autowire(param: 'jwt_public_key')]
        string $publicKeyPath,
        #[Autowire(env: 'JWT_ISSUER')]
        private readonly string $issuer,
        #[Autowire(env: 'JWT_AUDIENCE')]
        private readonly string $audience,
        #[Autowire(env: 'JWT_PASSPHRASE')]
        private readonly string $passphrase = '',
        #[Autowire(env: 'int:JWT_TTL')]
        private readonly int $tokenTtl = 3600
    ) {
        $this->configuration = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file($privateKeyPath, $this->passphrase),
            InMemory::file($publicKeyPath)
        );
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
            ->withClaim('sub', $userId)
            ->withClaim('email', $email)
            ->getToken($this->configuration->signer(), $this->configuration->signingKey());

        return $token->toString();
    }
}
