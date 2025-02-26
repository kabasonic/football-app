<?php

declare(strict_types=1);

namespace App\Tests\Application\Handler;

use App\Application\Dto\TeamDto;
use App\Application\Query\GetTeamQuery;
use App\Application\Handler\GetTeamQueryHandler;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetTeamQueryHandlerTest extends TestCase
{
    public function testHandleReturnsTeamDto(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new GetTeamQueryHandler($teamRepository);

        $teamIdValue = '01JMZVGN8Y45JEM3HMT8MEGMC6';
        $teamId = new TeamId($teamIdValue);

        $team = Mockery::mock(Team::class);
        $team->shouldReceive('getId')->andReturn($teamId);
        $team->shouldReceive('getName')->andReturn('FC Example');
        $team->shouldReceive('getCity')->andReturn('Example City');
        $team->shouldReceive('getYearFounded')->andReturn(1990);
        $team->shouldReceive('getStadiumName')->andReturn('Example Stadium');
        $team->shouldReceive('getPlayers')->andReturn(new ArrayCollection());

        $teamRepository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($arg) => $arg instanceof TeamId && $arg->getValue() === $teamIdValue))
            ->andReturn($team);

        $query = new GetTeamQuery($teamIdValue);

        // Act
        $result = $handler($query);

        // Assert
        $this->assertInstanceOf(TeamDto::class, $result);
        $this->assertEquals($teamIdValue, $result->id);
        $this->assertEquals('FC Example', $result->name);
        $this->assertEquals('Example City', $result->city);
        $this->assertEquals(1990, $result->yearFounded);
        $this->assertEquals('Example Stadium', $result->stadiumName);
        $this->assertEmpty($result->players);
    }

    public function testHandleThrowsExceptionWhenTeamNotFound(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new GetTeamQueryHandler($teamRepository);

        $teamIdValue = '01JMZVGN8Y45JEM3HMT8MEGMC6';

        $teamRepository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($arg) => $arg instanceof TeamId && $arg->getValue() === $teamIdValue))
            ->andReturnNull();

        $query = new GetTeamQuery($teamIdValue);

        // Assert
        $this->expectException(TeamNotFoundException::class);

        // Act
        $handler($query);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
