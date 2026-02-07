<?php

namespace App\Infrastructure\GraphQL\Resolver;

use App\Infrastructure\GraphQL\Type\UserType;
use App\Infrastructure\Security\User\UserContext;
use Overblog\GraphQLBundle\Annotation as GQL;

#[GQL\Provider]
final readonly class MeQueryResolver
{
    public function __construct(
        private UserContext $userContext
    )
    {
    }

    #[GQL\Query(name: "me", type: 'User')]
    public function __invoke(): ?UserType
    {
        $user = $this->userContext->getUser();

        if (null !== $user) {
            return UserType::fromReadModel($user);
        }

        return null;
    }
}
