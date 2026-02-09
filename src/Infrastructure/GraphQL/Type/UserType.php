<?php

namespace App\Infrastructure\GraphQL\Type;

use App\Application\ReadModel\UserReadModel;
use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
#[GQL\Type(name: 'User')]
readonly class UserType
{
    public function __construct(
        #[GQL\Field(type: 'Int!')]
        public int $id,

        #[GQL\Field(type: 'String!')]
        public string $name,

        #[GQL\Field(type: 'String!')]
        public string $username,

        #[GQL\Field(type: 'String!')]
        public string $email
    )
    {}

    public static function fromReadModel(UserReadModel $readModel): self
    {
        return new self(
            $readModel->id,
            $readModel->name,
            $readModel->username,
            $readModel->email
        );
    }
}
