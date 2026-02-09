<?php

namespace App\Infrastructure\GraphQL\Resolver;

use App\Application\Command\CreateTaskCommand;
use App\Infrastructure\GraphQL\Input\CreateTaskInput;
use App\Infrastructure\Security\User\UserContext;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Uid\Uuid;
use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\Messenger\MessageBusInterface;

#[GQL\Provider]
class TaskMutationResolver
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $commandBus,
        private readonly UserContext $userContext
    )
    {
        $this->messageBus = $commandBus;
    }

    #[GQL\Mutation(name: "createTask", type: "TaskPayload")]
    #[GQL\Arg(name: 'createTaskInput', type: 'CreateTaskInput!', description: 'Payload z danymi nowego zadania')]
    public function createTask(CreateTaskInput $createTaskInput): array
    {
        $authorId = $this->userContext->getCurrentUser()->id;
        $taskId = Uuid::v4()->toRfc4122();

        $this->handle(new CreateTaskCommand(
            $taskId,
            $createTaskInput->title,
            $createTaskInput->description,
            $authorId
        ));

        return [
            'id' => 2,
            'status' => 'TODO'
        ];
    }
}
