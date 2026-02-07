<?php

namespace App\Tests\Infrastructure\Security\CommandHandler;

use App\Application\FilterCriteria\UserFilterCriteria;
use App\Application\Repository\UserRepositoryInterface;
use App\Application\Security\Jwt\JwtFactoryInterface;
use App\Domain\Entity\User;
use App\Infrastructure\Security\Command\LoginCommand;
use App\Infrastructure\Security\CommandHandler\LoginCommandHandler;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class LoginCommandHandlerTest extends TestCase
{
    private UserRepositoryInterface&MockObject $userRepository;
    private JwtFactoryInterface&MockObject $jwtFactory;
    private LoginCommandHandler $loginCommandHandler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->jwtFactory = $this->createMock(JwtFactoryInterface::class);
        $this->loginCommandHandler = new LoginCommandHandler($this->userRepository, $this->jwtFactory);
    }

    #[Test]
    public function it_returns_token_on_successful_login(): void
    {
        $email = 'test@example.com';
        $userId = 123;
        $token = 'valid.jwt.token';
        $command = LoginCommand::create($email);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);
        $user->method('getEmail')->willReturn($email);

        $this->userRepository
            ->expects($this->once())
            ->method('findByCriteria')
            ->with($this->isInstanceOf(UserFilterCriteria::class))
            ->willReturn([$user]);

        $this->jwtFactory
            ->expects($this->once())
            ->method('createToken')
            ->with((string) $userId, $email)
            ->willReturn($token);

        $result = $this->loginCommandHandler->__invoke($command);

        $this->assertSame($token, $result);
    }

    #[Test]
    #[DataProvider('invalidEmailProvider')]
    public function it_throws_exception_for_invalid_email_format(string $invalidEmail): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The email \"$invalidEmail\" is not a valid email address.");

        $this->loginCommandHandler->__invoke(LoginCommand::create($invalidEmail));
    }

    public static function invalidEmailProvider(): iterable
    {
        yield ['not-an-email'];
        yield ['test@'];
        yield ['@example.com'];
        yield ['test.example.com'];
    }

    #[Test]
    #[DataProvider('securityFailureProvider')]
    public function it_throws_generic_exception_on_auth_failure(array $foundUsers): void
    {
        $this->userRepository
            ->method('findByCriteria')
            ->willReturn($foundUsers);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Nieprawidłowe dane logowania.');

        $this->loginCommandHandler->__invoke(LoginCommand::create('user@example.com'));
    }

    public static function securityFailureProvider(): iterable
    {
        yield 'user_not_found' => [[]];
        yield 'multiple_users_found' => [[
            'user1' => 'mock_obj',
            'user2' => 'mock_obj'
        ]];
    }
}
