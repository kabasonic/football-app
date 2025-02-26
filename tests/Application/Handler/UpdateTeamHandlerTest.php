<?php

declare(strict_types=1);

namespace App\Tests\Application\Handler;

use App\Application\Command\UpdateTeamCommand;
use App\Application\Dto\TeamDto;
use App\Application\Handler\UpdateTeamHandler;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use PHPUnit\Framework\TestCase;

class UpdateTeamHandlerTest extends TestCase
{
    public function testHandleUpdatesExistingTeam(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new UpdateTeamHandler($teamRepository);

        $teamIdValue = '01JMZVGN8Y45JEM3HMT8MEGMC6';
        $teamId = new TeamId($teamIdValue);
        $team = Mockery::mock(Team::class);

        $teamRepository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($arg) => $arg instanceof TeamId && $arg->getValue() === $teamIdValue))
            ->andReturn($team);

        $team->shouldReceive('update')
            ->once()
            ->with('Updated Name', 'Updated City', 2000, 'Updated Stadium');

        $teamRepository->shouldReceive('save')
            ->once()
            ->with($team);

        $team->shouldReceive('getId')->andReturn($teamId);
        $team->shouldReceive('getName')->andReturn('Updated Name');
        $team->shouldReceive('getCity')->andReturn('Updated City');
        $team->shouldReceive('getYearFounded')->andReturn(2000);
        $team->shouldReceive('getStadiumName')->andReturn('Updated Stadium');
        $team->shouldReceive('getPlayers')->andReturn(new ArrayCollection([]));

        $command = new UpdateTeamCommand(
            id: $teamIdValue,
            name: 'Updated Name',
            city: 'Updated City',
            yearFounded: 2000,
            stadiumName: 'Updated Stadium'
        );

        // Act
        $result = $handler($command);

        // Assert
        $this->assertInstanceOf(TeamDto::class, $result);
        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals('Updated City', $result->city);
        $this->assertEquals(2000, $result->yearFounded);
        $this->assertEquals('Updated Stadium', $result->stadiumName);
    }

    public function testHandleThrowsExceptionWhenTeamNotFound(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new UpdateTeamHandler($teamRepository);

        $teamIdValue = '01JMZVGN8Y45JEM3HMT8MEGMC6';

        $teamRepository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($arg) => $arg instanceof TeamId && $arg->getValue() === $teamIdValue))
            ->andReturnNull();

        $command = new UpdateTeamCommand(
            id: $teamIdValue,
            name: 'Updated Name',
            city: 'Updated City',
            yearFounded: 2000,
            stadiumName: 'Updated Stadium'
        );

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
