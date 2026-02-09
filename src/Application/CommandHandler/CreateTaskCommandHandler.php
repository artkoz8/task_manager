<?php

namespace App\Application\CommandHandler;

use App\Application\Command\CreateTaskCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
class CreateTaskCommandHandler
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $commandBus,
    )
    {
        $this->messageBus = $commandBus;
    }

    public function __invoke(CreateTaskCommand $command): void
    {
        // TODO: Implement __invoke() method.
        return;
    }
}
