<?php

namespace App\Application\ReadModel;

interface TaskReadModelGatewayInterface
{
    public function insert(TaskReadModel $readModel): void;

    public function update(TaskReadModel $readModel): void;
}
