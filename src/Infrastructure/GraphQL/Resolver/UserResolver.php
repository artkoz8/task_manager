<?php

namespace App\Infrastructure\GraphQL\Resolver;

use App\Application\Query\GetUsersQuery;
use App\Application\ReadModel\UserReadModel;
use App\Infrastructure\GraphQL\Type\UserType;
use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

//#[GQL\Type(name: 'Query')]
#[GQL\Provider]
class UserResolver
{
    use HandleTrait;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    #[GQL\Query(name: 'getUsers', type: '[User!]')]
    public function __invoke(): array
    {
        /** @var UserReadModel[] $readModels */
        $readModels = $this->handle(new GetUsersQuery());

        return array_map(
            fn(UserReadModel $model) => UserType::fromReadModel($model),
            $readModels
        );
    }
}
