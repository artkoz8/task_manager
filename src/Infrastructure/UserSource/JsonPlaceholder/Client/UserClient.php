<?php

namespace App\Infrastructure\UserSource\JsonPlaceholder\Client;

use App\Application\FilterCriteria\UserFilterCriteria;
use App\Application\Repository\UserSourceStrategyInterface;
use App\Domain\Entity\User;
use App\Infrastructure\UserSource\JsonPlaceholder\Filter\HttpQueryMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AutoconfigureTag(name: 'app.user_source_strategy')]
readonly class UserClient implements UserSourceStrategyInterface
{
    public function __construct(
        private HttpQueryMapper $httpQueryMapper,
        private HttpClientInterface $httpClient,
        #[Autowire(env: 'JSON_PLACEHOLDER_API_URL')]
        private string $apiUrl,
        #[Autowire(env: 'JSON_PLACEHOLDER_SOURCE_NAME')]
        private string $sourceName,
    ) {
    }

    public function findByCriteria(UserFilterCriteria $criteria): array
    {
        $response = $this->httpClient->request(
            'GET',
            $this->apiUrl,
            [
                'query' => $this->httpQueryMapper->toQueryParams($criteria)
            ]
        );

        $data = $response->toArray();

        return array_map(
            fn(array $userData) => $this->mapToDomain($userData),
            $data
        );
    }

    public function supports(string $sourceType): bool
    {
        return $this->sourceName === $sourceType;
    }

    private function mapToDomain(array $data): User
    {
        return User::create(
            (int) $data['id'],
            (string) $data['name'],
            (string) $data['username'],
            (string) $data['email']
        );
    }
}
