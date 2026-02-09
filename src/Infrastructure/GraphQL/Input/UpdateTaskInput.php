<?php

namespace App\Infrastructure\GraphQL\Input;

use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
#[GQL\Input(name: "UpdateTaskInput")]
class UpdateTaskInput
{
    #[GQL\Field(type: "String")]
    public ?string $title = null;

    #[GQL\Field(type: "String")]
    public ?string $description = null;
}
