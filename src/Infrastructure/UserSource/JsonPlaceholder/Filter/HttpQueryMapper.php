<?php

namespace App\Infrastructure\UserSource\JsonPlaceholder\Filter;

use App\Application\FilterCriteria\UserFilterCriteria;

final readonly class HttpQueryMapper
{
    public function toQueryParams(UserFilterCriteria $criteria): array
    {
        return array_filter([
            'id' => $criteria->getId(),
            'username' => $criteria->getUsername(),
            'email' => $criteria->getEmail(),
        ], fn($value) => $value !== null);
    }
}
