<?php

namespace App\Infrastructure\UserSource\JsonPlaceholder\Filter;

use App\Application\FilterCriteria\UserFilterCriteria;

final readonly class HttpQueryMapper
{
    public function toQueryParams(UserFilterCriteria $criteria): array
    {
        return array_filter([
            'id' => $criteria->id,
            'username' => $criteria->username,
            'email' => $criteria->email,
        ], fn($value) => $value !== null);
    }
}
