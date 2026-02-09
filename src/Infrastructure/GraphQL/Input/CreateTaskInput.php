<?php

namespace App\Infrastructure\GraphQL\Input;

use Overblog\GraphQLBundle\Annotation as GQL;

#[GQL\Input(name: "CreateTaskInput")]
class CreateTaskInput
{
    #[GQL\Field(type: "String!")]
    public ?string $title = null;

    #[GQL\Field(type: "String!")]
    public ?string $description = null;
}
