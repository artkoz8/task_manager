<?php

namespace App\Infrastructure\GraphQL\Resolver;

use App\Infrastructure\GraphQL\Type\AuthPayload;
use App\Infrastructure\Security\Command\LoginCommand;
use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

#[GQL\Provider]
class LoginMutationResolver
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    #[GQL\Mutation(name: "login", type: "AuthPayload!")]
    #[GQL\Arg(name: "email", type: "String!")]
    public function __invoke(string $email): AuthPayload
    {
        /** @var string $token */
        $token = $this->handle(LoginCommand::create($email));

        return new AuthPayload(token: $token);
    }
}
