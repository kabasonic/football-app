<?php

declare(strict_types=1);

namespace App\Tests\Application\Handler;

use App\Application\Command\RelocateTeamCommand;
use App\Application\Handler\RelocateTeamHandler;
use App\Application\Dto\TeamDto;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class RelocateTeamHandlerTest extends TestCase
{
    public function testHandleRelocatesExistingTeam(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $handler = new RelocateTeamHandler($teamRepository, $eventDispatcher);

        $teamIdValue = '01JMZVGN8Y45JEM3HMT8MEGMC6';
        $newCity = 'New York';

        $team = Mockery::mock(Team::class);
        $team->shouldReceive('getId')->andReturn(new TeamId($teamIdValue));
        $team->shouldReceive('getName')->andReturn('Old Team Name');
        $team->shouldReceive('getCity')->andReturn($newCity);
        $team->shouldReceive('getYearFounded')->andReturn(1995);
        $team->shouldReceive('getStadiumName')->andReturn('Old Stadium');
        $team->shouldReceive('getPlayers')->andReturn(new ArrayCollection());

        $team->shouldReceive('relocate')
            ->once()
            ->with($newCity);

        $team->shouldReceive('pullDomainEvents')
            ->once()
            ->andReturn([]);

        $teamRepository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($arg) => $arg instanceof TeamId && $arg->getValue() === $teamIdValue))
            ->andReturn($team);

        $teamRepository->shouldReceive('save')
            ->once()
            ->with($team);

        $eventDispatcher->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $command = new RelocateTeamCommand(teamId: $teamIdValue, city: $newCity);

        // Act
        $result = $handler($command);

        // Assert
        $this->assertInstanceOf(TeamDto::class, $result);
        $this->assertEquals($teamIdValue, $result->id);
        $this->assertEquals($newCity, $result->city);
    }


    public function testHandleThrowsExceptionWhenTeamNotFound(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $handler = new RelocateTeamHandler($teamRepository, $eventDispatcher);

        $teamIdValue = '01JMZVGN8Y45JEM3HMT8MEGMC6';

        $teamRepository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($arg) => $arg instanceof TeamId && $arg->getValue() === $teamIdValue))
            ->andReturnNull();

        $command = new RelocateTeamCommand(teamId: $teamIdValue, city: 'New City');

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
