<?php

namespace App\Infrastructure\Security\Jwt;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Clock\NativeClock;

class JwtAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly Configuration $jwtConfig,
        #[Autowire(env: 'JWT_ISSUER')]
        private readonly string $issuer,
        #[Autowire(env: 'JWT_AUDIENCE')]
        private readonly string $audience
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        try {
            $tokenString = $this->extractToken($request);
            $token = $this->jwtConfig->parser()->parse($tokenString);

            $this->validateToken($token);

            return $this->createPassport($token);
        } catch (InvalidTokenStructure) {
            throw new CustomUserMessageAuthenticationException('Nieprawidłowa struktura tokena.');
        } catch (AuthenticationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new CustomUserMessageAuthenticationException('Wystąpił błąd podczas autoryzacji.');
        }
    }

    private function extractToken(Request $request): string
    {
        $authorization = $request->headers->get('Authorization');

        if (null === $authorization || !str_starts_with($authorization, 'Bearer ')) {
            throw new CustomUserMessageAuthenticationException('Brak lub nieprawidłowy format tokena autoryzacji.');
        }

        return str_replace('Bearer ', '', $authorization);
    }

    private function validateToken(Token $token): void
    {
        $constraints = [
            new SignedWith($this->jwtConfig->signer(), $this->jwtConfig->verificationKey()),
            new IssuedBy($this->issuer),
            new PermittedFor($this->audience),
            new LooseValidAt(new NativeClock())
        ];

        if (!$this->jwtConfig->validator()->validate($token, ...$constraints)) {
            throw new CustomUserMessageAuthenticationException('Token wygasł lub jest nieprawidłowy.');
        }
    }

    private function createPassport(Token $token): Passport
    {
        $userId = $token->claims()->get('sub');

        if (!$userId) {
            throw new CustomUserMessageAuthenticationException('Token nie zawiera identyfikatora użytkownika.');
        }

        return new SelfValidatingPassport(new UserBadge((string) $userId));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'errors' => [
                [
                    'message' => 'Błąd autoryzacji',
                    'extensions' => [
                        'reason' => strtr($exception->getMessageKey(), $exception->getMessageData()),
                        'code' => 'UNAUTHORIZED'
                    ]
                ]
            ]
        ], Response::HTTP_UNAUTHORIZED);
    }
}
