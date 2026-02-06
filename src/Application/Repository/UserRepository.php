<?php

namespace App\Application\Repository;

use App\Domain\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class UserRepository implements UserRepositoryInterface
{
    /**
     * @param iterable<UserSourceStrategyInterface> $strategies
     */
    public function __construct(
        #[AutowireIterator(tag: 'app.user_source_strategy')]
        private iterable $strategies,
        #[Autowire(env: 'USER_SOURCE_NAME')]
        private string $activeSource
    ) {
    }

    public function fetchAll(): array
    {
        return $this->getStrategy()->fetchAll();
    }

    public function fetchById(int $id): ?User
    {
        return $this->getStrategy()->fetchById($id);
    }

    public function fetchByEmail(string $email): ?User
    {
        return $this->getStrategy()->fetchByEmail($email);
    }

    public function fetchByUsername(string $username): ?User
    {
        return $this->getStrategy()->fetchByUsername($username);
    }

    private function getStrategy(): UserSourceStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($this->activeSource)) {
                return $strategy;
            }
        }

        throw new \RuntimeException(sprintf('No strategy found for source: %s', $this->activeSource));
    }
}
