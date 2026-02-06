<?php

namespace App\Application\Repository;

use App\Application\FilterCriteria\UserFilterCriteria;
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

    /**
     * @return User[]
     */
    public function findByCriteria(UserFilterCriteria $criteria): array
    {
        return $this->getStrategy()->findByCriteria($criteria);
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
