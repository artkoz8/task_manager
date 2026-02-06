<?php

namespace App\Infrastructure\Repository;

use App\Application\Repository\UserSourceStrategyInterface;
use App\Domain\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AutoconfigureTag(name: 'app.user_source_strategy')]
readonly class JsonPlaceholderUserStrategy implements UserSourceStrategyInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire(env: 'JSON_PLACEHOLDER_API_URL')]
        private string $apiUrl,
        #[Autowire(env: 'JSON_PLACEHOLDER_SOURCE_NAME')]
        private string $sourceName,
    ) {
    }

    public function fetchAll(): array
    {
        $response = $this->httpClient->request('GET', $this->apiUrl);
        $data = $response->toArray();

        return array_map(
            fn(array $userData) => $this->mapToDomain($userData),
            $data
        );
    }

    public function fetchById(int $id): ?User
    {
        $response = $this->httpClient->request('GET', $this->apiUrl . '/' . $id);

        if (404 === $response->getStatusCode()) {
            return null;
        }

        $userData = $response->toArray();

        return $this->mapToDomain($userData);
    }

    public function fetchByEmail(string $email): ?User
    {
        $response = $this->httpClient->request('GET', $this->apiUrl, [
            'query' => ['email' => $email]
        ]);

        $data = $response->toArray();

        return isset($data[0]) ? $this->mapToDomain($data) : null;
    }

    public function fetchByUsername(string $username): ?User
    {
        $response = $this->httpClient->request('GET', $this->apiUrl, [
            'query' => ['username' => $username]
        ]);

        $data = $response->toArray();

        return isset($data[0]) ? $this->mapToDomain($data) : null;
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
