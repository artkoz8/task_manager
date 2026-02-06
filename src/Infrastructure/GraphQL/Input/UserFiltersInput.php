<?php

namespace App\Infrastructure\GraphQL\Input;

use Overblog\GraphQLBundle\Annotation as GQL;

#[GQL\Input(name: "UserFiltersInput")]
class UserFiltersInput
{
    #[GQL\Field(type: "Int")]
    public ?int $id = null;

    #[GQL\Field(type: "String")]
    public ?string $username = null;

    #[GQL\Field(type: "String")]
    public ?string $email = null;
}
