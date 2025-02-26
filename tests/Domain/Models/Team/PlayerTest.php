<?php

namespace App\Tests\Domain\Models\Team;

use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\Entity\Player;
use App\Domain\Models\Team\ValueObject\PlayerId;
use PHPUnit\Framework\TestCase;
use Mockery;

class PlayerTest extends TestCase
{
    public function testPlayerConstructor(): void
    {
        // Arrange
        $playerId = new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC6');

        // Act
        $player = new Player($playerId, 'John', 'Doe', 25, 'FORWARD');

        // Assert
        $this->assertEquals('John', $player->getFirstName());
        $this->assertEquals('Doe', $player->getLastName());
        $this->assertEquals(25, $player->getAge());
        $this->assertEquals('FORWARD', $player->getPosition());
    }

    public function testSetTeamAndGetTeam(): void
    {
        // Arrange
        $playerId = new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC6');
        $team = Mockery::mock(Team::class);

        // Act
        $player = new Player($playerId, 'John', 'Doe', 25, 'Midfielder');
        $player->setTeam($team);

        // Assert
        $this->assertSame($team, $player->getTeam());
    }

    public function testUpdatePlayerSuccessfully(): void
    {
        // Arrange
        $playerId = new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC6');
        $player = new Player($playerId, 'John', 'Doe', 25, 'FORWARD');

        // Act
        $player->update('Jane', 'Smith', 30, 'DEFENDER');

        // Assert
        $this->assertEquals('Jane', $player->getFirstName());
        $this->assertEquals('Smith', $player->getLastName());
        $this->assertEquals(30, $player->getAge());
        $this->assertEquals('DEFENDER', $player->getPosition());
    }

    public function testGetId(): void
    {
        // Arrange
        $playerId = new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC6');
        $player = new Player($playerId, 'John', 'Doe', 25, 'DEFENDER');

        // Act
        $playerIdReturned = $player->getId();

        // Assert
        $this->assertEquals($playerId, $playerIdReturned);
    }

    public function testGetFirstName(): void
    {
        // Arrange
        $playerId = new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC6');
        $player = new Player($playerId, 'John', 'Doe', 25, 'DEFENDER');

        // Act & Assert
        $this->assertEquals('John', $player->getFirstName());
    }

    public function testGetLastName(): void
    {
        // Arrange
        $playerId = new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC6');
        $player = new Player($playerId, 'John', 'Doe', 25, 'DEFENDER');

        // Act & Assert
        $this->assertEquals('Doe', $player->getLastName());
    }

    public function testGetAge(): void
    {
        // Arrange
        $playerId = new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC6');
        $player = new Player($playerId, 'John', 'Doe', 25, 'DEFENDER');

        // Act & Assert
        $this->assertEquals(25, $player->getAge());
    }

    public function testGetPosition(): void
    {
        // Arrange
        $playerId = new PlayerId('01JMZVGN8Y45JEM3HMT8MEGMC6');
        $player = new Player($playerId, 'John', 'Doe', 25, 'DEFENDER');

        // Act & Assert
        $this->assertEquals('DEFENDER', $player->getPosition());
    }
}
