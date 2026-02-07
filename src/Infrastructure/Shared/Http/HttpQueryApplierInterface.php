<?php

namespace App\Infrastructure\Shared\Http;

interface HttpQueryApplierInterface
{
    public function applyToQuery(array $currentParams): array;
}
