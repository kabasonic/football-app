<?php

namespace App\Tests\Domain\Models\Team;

use App\Domain\Event\TeamRelocatedEvent;
use App\Domain\Exception\InvalidLocationChangeException;
use App\Domain\Exception\PlayerNotFoundException;
use App\Domain\Exception\TeamPlayerLimitExceededException;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\Entity\Player;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Shared\Domain\Services\UlidService;
use PHPUnit\Framework\TestCase;
use Mockery;

class TeamTest extends TestCase
{
    public function testAddPlayerSuccessfully(): void
    {
        // Arrange
        $teamId = new TeamId('01JMZVGN8Y45JEM3HMT8MEGMC7');
        $team = Team::create($teamId, 'Team A', 'City A', 1990, 'Stadium A');

        $playerId = new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC6');
        $player = Mockery::mock(Player::class);
        $player->shouldReceive('getId')->andReturn($playerId);
        $player->shouldReceive('setTeam')->with($team)->once();

        // Act
        $team->addPlayer($player);

        // Assert
        $this->assertCount(1, $team->getPlayers());
        $this->assertSame($player, $team->getPlayers()->first());
    }

    public function testAddPlayerThrowsExceptionWhenPlayerLimitExceeded(): void
    {
        // Arrange
        $teamId = new TeamId('01JMZVGN8Y45JEM3HMT8MEGMC7');
        $team = Team::create($teamId, 'Team A', 'City A', 1990, 'Stadium A');

        for ($i = 0; $i < 11; $i++) {
            $playerId = new PlayerId(UlidService::generate());
            $player = Mockery::mock(Player::class);
            $player->shouldReceive('setTeam')->with($team)->once();
            $player->shouldReceive('getId')->andReturn($playerId);
            $team->addPlayer($player);
        }

        $newPlayerId = new PlayerId('01H8Y6V5T8MEGJEM3HMTZVGNC9');
        $newPlayer = Mockery::mock(Player::class);
        $newPlayer->shouldReceive('getId')->andReturn($newPlayerId);

        // Act & Assert
        $this->expectException(TeamPlayerLimitExceededException::class);
        $team->addPlayer($newPlayer);
    }

    public function testFindPlayerById(): void
    {
        // Arrange
        $teamId = new TeamId('01JMZVGN8Y45JEM3HMT8MEGMC7');
        $team = Team::create($teamId, 'Team A', 'City A', 1990, 'Stadium A');

        $playerId = new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC6');
        $player = Mockery::mock(Player::class);
        $player->shouldReceive('getId')->andReturn($playerId);
        $player->shouldReceive('setTeam')->with($team)->once();

        $team->addPlayer($player);

        // Act
        $foundPlayer = $team->findPlayerById($playerId);

        // Assert
        $this->assertSame($player, $foundPlayer);
    }

    public function testUpdatePlayerSuccessfully(): void
    {
        // Arrange
        $teamId = new TeamId('01JMZVGN8Y45JEM3HMT8MEGMC7');
        $team = Team::create($teamId, 'Team A', 'City A', 1990, 'Stadium A');

        $playerId = new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC6');
        $player = Mockery::mock(Player::class);
        $player->shouldReceive('getId')->andReturn($playerId);
        $player->shouldReceive('update')->with('John', 'Doe', 25, 'Midfielder')->once();
        $player->shouldReceive('setTeam')->with($team)->once();

        $team->addPlayer($player);

        // Act
        $team->updatePlayer($playerId, 'John', 'Doe', 25, 'Midfielder');

        // Assert
        $this->assertTrue($team->getPlayers()->contains($player));
    }


    public function testUpdatePlayerThrowsExceptionWhenPlayerNotFound(): void
    {
        // Arrange
        $teamId = new TeamId('01JMZVGN8Y45JEM3HMT8MEGMC7');
        $team = Team::create($teamId, 'Team A', 'City A', 1990, 'Stadium A');

        $playerId = new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC6');

        // Act & Assert
        $this->expectException(PlayerNotFoundException::class);
        $team->updatePlayer($playerId, 'John', 'Doe', 25, 'Midfielder');
    }

    public function testRemovePlayerSuccessfully(): void
    {
        // Arrange
        $teamId = new TeamId('01JMZVGN8Y45JEM3HMT8MEGMC7');
        $team = Team::create($teamId, 'Team A', 'City A', 1990, 'Stadium A');

        $playerId = new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC6');
        $player = Mockery::mock(Player::class);
        $player->shouldReceive('getId')->andReturn($playerId);
        $player->shouldReceive('setTeam')->with($team)->once();

        $team->addPlayer($player);

        // Act
        $team->removePlayer($player);

        // Assert
        $this->assertCount(0, $team->getPlayers());
    }

    public function testRemovePlayerThrowsExceptionWhenPlayerNotFound(): void
    {
        // Arrange
        $teamId = new TeamId('01JMZVGN8Y45JEM3HMT8MEGMC7');
        $team = Team::create($teamId, 'Team A', 'City A', 1990, 'Stadium A');

        $playerId = new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC6');
        $player = Mockery::mock(Player::class);
        $player->shouldReceive('getId')->andReturn($playerId);
        $player->shouldReceive('setTeam')->with($team)->once();

        // Act & Assert
        $this->expectException(PlayerNotFoundException::class);
        $team->removePlayer($player);
    }

    public function testRelocateTeamSuccessfully(): void
    {
        // Arrange
        $teamId = new TeamId('01JMZVGN8Y45JEM3HMT8MEGMC7');
        $team = Team::create($teamId, 'Team A', 'City A', 1990, 'Stadium A');

        // Act
        $team->relocate('City B');

        // Assert
        $this->assertSame('City B', $team->getCity());
        $this->assertTrue($team->hasRecordedDomainEvent(TeamRelocatedEvent::class));
    }

    public function testRelocateTeamThrowsExceptionWhenNewCityIsSame(): void
    {
        // Arrange
        $teamId = new TeamId('01JMZVGN8Y45JEM3HMT8MEGMC7');
        $team = Team::create($teamId, 'Team A', 'City A', 1990, 'Stadium A');

        // Act & Assert
        $this->expectException(InvalidLocationChangeException::class);
        $team->relocate('City A');
    }
}
