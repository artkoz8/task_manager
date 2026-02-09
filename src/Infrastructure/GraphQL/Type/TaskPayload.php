<?php

namespace App\Infrastructure\GraphQL\Type;

use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
#[GQL\Type(name: "TaskPayload")]
final readonly class TaskPayload
{
    public function __construct(
        #[GQL\Field(type: "String!")]
        public string $id,
        #[GQL\Field(type: "String!")]
        public string $status,
    ) {
    }
}
