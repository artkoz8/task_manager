<?php

namespace App\Infrastructure\GraphQL\Resolver;

use Overblog\GraphQLBundle\Annotation as GQL;

class PingResolver
{
    public function ping(): string
    {
        return 'pong 2';
    }
}
