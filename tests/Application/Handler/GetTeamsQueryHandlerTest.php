<?php

declare(strict_types=1);

namespace App\Tests\Application\Handler;

use App\Application\Dto\TeamDto;
use App\Application\Dto\TeamPlayerDto;
use App\Application\Handler\GetTeamsQueryHandler;
use App\Application\Query\GetTeamsQuery;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\Entity\Player;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetTeamsQueryHandlerTest extends TestCase
{
    public function testHandleReturnsTeams(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new GetTeamsQueryHandler($teamRepository);

        $player = Mockery::mock(Player::class);
        $player->shouldReceive('getId')->andReturn(new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC7'));
        $player->shouldReceive('getFirstName')->andReturn('John');
        $player->shouldReceive('getLastName')->andReturn('Doe');
        $player->shouldReceive('getAge')->andReturn(25);
        $player->shouldReceive('getPosition')->andReturn('Forward');

        $team = Mockery::mock(Team::class);
        $team->shouldReceive('getId')->andReturn(new TeamId('01JMZVGN8Y45JEM3HMT8MEGMC6'));
        $team->shouldReceive('getName')->andReturn('Team A');
        $team->shouldReceive('getCity')->andReturn('City X');
        $team->shouldReceive('getYearFounded')->andReturn(1990);
        $team->shouldReceive('getStadiumName')->andReturn('Stadium Y');
        $team->shouldReceive('getPlayers->getValues')->andReturn([$player]);

        $teamRepository->shouldReceive('findAll')->once()->andReturn([$team]);

        $query = new GetTeamsQuery();

        // Act
        $result = $handler($query);

        // Assert
        $this->assertCount(1, $result);
        $this->assertInstanceOf(TeamDto::class, $result[0]);
        $this->assertSame('Team A', $result[0]->name);
        $this->assertCount(1, $result[0]->players);
        $this->assertInstanceOf(TeamPlayerDto::class, $result[0]->players[0]);
        $this->assertSame('John', $result[0]->players[0]->firstName);
    }

    public function testHandleReturnsEmptyArrayWhenNoTeams(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new GetTeamsQueryHandler($teamRepository);

        $teamRepository->shouldReceive('findAll')->once()->andReturn([]);

        $query = new GetTeamsQuery();

        // Act
        $result = $handler($query);

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
