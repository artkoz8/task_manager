<?php

namespace App\Tests\Application\Repository;

use App\Application\FilterCriteria\UserFilterCriteria;
use App\Application\Repository\UserRepository;
use App\Application\Repository\UserSourceStrategyInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserRepositoryStrategySelectionTest extends TestCase
{
    #[Test]
    #[DataProvider('repositoryMethodsProvider')]
    public function it_should_delegate_all_calls_to_supported_strategy(string $method, array $args, mixed $returnValue): void
    {
        $activeSource = 'active_source';

        $wrongStrategy = $this->createMock(UserSourceStrategyInterface::class);
        $wrongStrategy->expects($this->once())
            ->method('supports')
            ->with($activeSource)
            ->willReturn(false);

        $correctStrategy = $this->createMock(UserSourceStrategyInterface::class);
        $correctStrategy->expects($this->once())
            ->method('supports')
            ->with($activeSource)
            ->willReturn(true);

        $correctStrategy->expects($this->once())
            ->method($method)
            ->with(...$args)
            ->willReturn($returnValue);

        $repository = new UserRepository([$wrongStrategy, $correctStrategy], $activeSource);

        $result = $repository->$method(...$args);

        $this->assertEquals($returnValue, $result);
    }

    #[Test]
    #[DataProvider('repositoryMethodsProvider')]
    public function it_throws_exception_when_no_strategy_supports_source(string $method, array $args, mixed $returnValue): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No strategy found for source: unknown');

        $repository = new UserRepository([], 'unknown');
        $repository->$method(...$args);
    }

    #[Test]
    #[DataProvider('repositoryMethodsProvider')]
    public function it_throws_exception_when_multiple_strategies_exist_but_none_supports_source(string $method, array $args, mixed $returnValue): void
    {
        $strategy1 = $this->createMock(UserSourceStrategyInterface::class);
        $strategy1
            ->method('supports')
            ->with('json_placeholder')
            ->willReturn(false);

        $strategy2 = $this->createMock(UserSourceStrategyInterface::class);
        $strategy2
            ->method('supports')
            ->with('json_placeholder')
            ->willReturn(false);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No strategy found for source: unknown');

        $repository = new UserRepository([], 'unknown');
        $repository->$method(...$args);
    }

    public static function repositoryMethodsProvider(): iterable
    {
        yield 'find by criteria' => [
            'method' => 'findByCriteria',
            'args' => [new UserFilterCriteria(username: 'jdoe')],
            'returnValue' => []
        ];
    }
}
