<?php

namespace App\Infrastructure\Security\User;

use App\Application\Query\GetUsersQuery;
use App\Application\ReadModel\UserReadModel;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Throwable;

class JwtUserProvider implements UserProviderInterface
{
    use HandleTrait;

    public function __construct(
        private MessageBusInterface $queryBus
    )
    {
        $this->messageBus = $queryBus;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            /** @var UserReadModel[] $users */
            $users = $this->handle(new GetUsersQuery(id: (int) $identifier));

            if (1 !== count($users)) {
                throw new AuthenticationException('Problem z autoryzacją dostępu.');
            }

            return AuthUserAdapter::create($users[0]);
        } catch (Throwable $e) {
            throw new AuthenticationException('Wystąpił błąd podczas weryfikacji tożsamości.', 0, $e);
        }
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$this->supportsClass($user::class)) {
            throw new UnsupportedUserException(sprintf('Instancje klasy "%s" nie są obsługiwane przez ten provider.', $user::class));
        }

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return is_subclass_of($class, UserInterface::class);
    }
}
