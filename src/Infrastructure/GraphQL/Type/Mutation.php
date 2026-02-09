<?php

namespace App\Infrastructure\GraphQL\Type;

use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
#[GQL\Type(name: 'Mutation')]
final readonly class Mutation
{}
