<?php

namespace App\Infrastructure\GraphQL\Type;

use App\Infrastructure\GraphQL\Resolver\PingResolver;
use Overblog\GraphQLBundle\Annotation as GQL;

#[GQL\Type(name: 'Query')]
final readonly class PingQuery
{
    public function __construct(
        private PingResolver $pingResolver,
    )
    {
    }

    #[GQL\Field(type: 'String!')]
    public function ping(): string
    {
        return $this->pingResolver->ping();
    }
}
