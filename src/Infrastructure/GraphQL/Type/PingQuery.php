<?php

namespace App\Infrastructure\GraphQL\Type;

use Overblog\GraphQLBundle\Annotation as GQL;

#[GQL\Type(name: 'Query')]
final readonly class PingQuery
{}
