<?php

namespace App\Infrastructure\GraphQL\Type;

use App\Application\ReadModel\TaskReadModel;
use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
#[GQL\Type(name: "TaskPayload")]
final readonly class TaskPayload
{
    private function __construct(
        #[GQL\Field(type: "String!")]
        public string $id,

        #[GQL\Field(type: "String!")]
        public string $authorId,

        #[GQL\Field(type: "String!")]
        public string $title,

        #[GQL\Field(type: "String!")]
        public string $description,

        #[GQL\Field(type: "String!")]
        public string $status,
    )
    {
    }

    public static function createFromReadModel(TaskReadModel $readModel): self
    {
        return new self(
            $readModel->id,
            $readModel->authorId,
            $readModel->title,
            $readModel->description,
            $readModel->status
        );
    }
}
