<?php

namespace App\Infrastructure\Security\CommandHandler;

use App\Application\FilterCriteria\UserFilterCriteria;
use App\Application\Repository\UserRepositoryInterface;
use App\Application\Security\Jwt\JwtFactoryInterface;
use App\Infrastructure\Security\Command\LoginCommand;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
readonly class LoginCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private JwtFactoryInterface $jwtFactory,
    )
    {
    }

    public function __invoke(LoginCommand $command): string
    {
        $this->validate($command->email);

        $userFilterCriteria  = UserFilterCriteria::createWithEmail($command->email);
        $users = $this->userRepository->findByCriteria($userFilterCriteria);

        if (count($users) !== 1) {
            throw new RuntimeException('Nieprawidłowe dane logowania.');
        }

        $user = $users[0];

        return $this->jwtFactory->createToken(
            (string) $user->getId(),
            $user->getEmail()
        );
    }

    private function validate(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf('The email "%s" is not a valid email address.', $email));
        }
    }
}
