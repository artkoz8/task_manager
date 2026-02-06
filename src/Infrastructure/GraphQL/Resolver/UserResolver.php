<?php

namespace App\Infrastructure\GraphQL\Resolver;

use App\Application\Query\GetUsersQuery;
use App\Application\ReadModel\UserReadModel;
use App\Infrastructure\GraphQL\Input\UserFiltersInput;
use App\Infrastructure\GraphQL\Type\UserType;
use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

#[GQL\Provider]
class UserResolver
{
    use HandleTrait;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    #[GQL\Query(name: 'getUsers', type: '[User!]')]
    #[GQL\Arg(name: 'id', type: 'Int', description: 'User name')]
    #[GQL\Arg(name: 'filters', type: 'UserFiltersInput', description: 'Filters')]
    public function __invoke(?int $id, ?UserFiltersInput $filters): array
    {
        /** @var UserReadModel[] $readModels */
        $readModels = $this->handle(GetUsersQuery::fromInput($filters));

        return array_map(
            fn(UserReadModel $model) => UserType::fromReadModel($model),
            $readModels
        );
    }
}
