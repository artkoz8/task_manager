<?php

namespace App\Infrastructure\GraphQL\Type;

use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
#[GQL\Type(name: 'AuthPayload')]
class AuthPayload
{
    public function __construct(
        #[GQL\Field(type: 'String!')]
        public string $token,
    ) {
    }
}
