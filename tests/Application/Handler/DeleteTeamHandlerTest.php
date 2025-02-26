<?php

declare(strict_types=1);

namespace App\Tests\Application\Handler;

use App\Application\Command\DeleteTeamCommand;
use App\Application\Handler\DeleteTeamHandler;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class DeleteTeamHandlerTest extends TestCase
{
    public function testHandleDeletesExistingTeam(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new DeleteTeamHandler($teamRepository);

        $teamIdValue = '01JMZVGN8Y45JEM3HMT8MEGMC6';
        $team = Mockery::mock(Team::class);

        $teamRepository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($arg) => $arg instanceof TeamId && $arg->getValue() === $teamIdValue))
            ->andReturn($team);

        $teamRepository->shouldReceive('delete')
            ->once()
            ->with($team);

        $command = new DeleteTeamCommand(id: $teamIdValue);

        // Act
        $handler($command);

        // Assert
        $this->expectNotToPerformAssertions();
    }

    public function testHandleThrowsExceptionWhenTeamNotFound(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new DeleteTeamHandler($teamRepository);

        $teamIdValue = '01JMZVGN8Y45JEM3HMT8MEGMC6';

        $teamRepository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($arg) => $arg instanceof TeamId && $arg->getValue() === $teamIdValue))
            ->andReturnNull();

        $command = new DeleteTeamCommand(id: $teamIdValue);

        // Assert
        $this->expectException(TeamNotFoundException::class);

        // Act
        $handler($command);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
