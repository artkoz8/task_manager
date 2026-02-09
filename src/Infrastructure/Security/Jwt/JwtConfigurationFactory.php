<?php

namespace App\Infrastructure\Security\Jwt;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class JwtConfigurationFactory
{
    public function __construct(
        #[Autowire(param: 'jwt_private_key')]
        private string $privateKeyPath,
        #[Autowire(param: 'jwt_public_key')]
        private string $publicKeyPath,
        #[Autowire(env: 'JWT_PASSPHRASE')]
        private string $passphrase = '',
    ) {
    }

    public function create(): Configuration
    {
        return Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file($this->privateKeyPath, $this->passphrase),
            InMemory::file($this->publicKeyPath)
        );
    }
}
