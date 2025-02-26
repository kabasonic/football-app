<?php

declare(strict_types=1);

namespace App\Tests\Application\Handler;

use App\Application\Command\AddPlayerToTeamCommand;
use App\Application\Dto\PlayerDto;
use App\Application\Handler\AddPlayerToTeamHandler;
use App\Domain\Models\Team\Entity\Player;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Exception\TeamPlayerLimitExceededException;
use Mockery;
use PHPUnit\Framework\TestCase;

class AddPlayerToTeamHandlerTest extends TestCase
{
    public function testAddPlayerToTeam(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new AddPlayerToTeamHandler($teamRepository);

        $validUlid = '01JMZVGN8Y45JEM3HMT8MEGMC6';
        $command = new AddPlayerToTeamCommand(
            teamId: $validUlid,
            firstName: 'John',
            lastName: 'Doe',
            age: 25,
            position: 'FORWARD'
        );

        $teamId = new TeamId($validUlid);
        $team = Mockery::mock(Team::class);
        $team->shouldReceive('getId')->andReturn($teamId);

        $teamRepository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($id) => $id->getValue() === $teamId->getValue()))
            ->andReturn($team);

        $team->shouldReceive('addPlayer')
            ->once()
            ->with(Mockery::type(Player::class));

        $teamRepository->shouldReceive('save')
            ->once()
            ->with($team);

        // Act
        $result = $handler($command);

        // Assert
        $this->assertInstanceOf(PlayerDto::class, $result);
        $this->assertEquals('John', $result->firstName);
        $this->assertEquals('Doe', $result->lastName);
        $this->assertEquals(25, $result->age);
        $this->assertEquals('FORWARD', $result->position);
    }

    public function testThrowsExceptionWhenTeamNotFound(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new AddPlayerToTeamHandler($teamRepository);

        $validUlid = '01JMZVGN8Y45JEM3HMT8MEGMC6';
        $command = new AddPlayerToTeamCommand(
            teamId: $validUlid,
            firstName: 'John',
            lastName: 'Doe',
            age: 25,
            position: 'Forward'
        );

        $teamId = new TeamId($validUlid);
        $teamRepository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($id) => $id->getValue() === $teamId->getValue()))
            ->andReturn(null);

        // Act & Assert
        $this->expectException(TeamNotFoundException::class);
        $handler($command);
    }

    public function testThrowsExceptionWhenPlayerLimitExceeded(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new AddPlayerToTeamHandler($teamRepository);

        $validUlid = '01JMZVGN8Y45JEM3HMT8MEGMC6';
        $command = new AddPlayerToTeamCommand(
            teamId: $validUlid,
            firstName: 'John',
            lastName: 'Doe',
            age: 25,
            position: 'Forward'
        );

        $teamId = new TeamId($validUlid);
        $team = Mockery::mock(Team::class);
        $team->shouldReceive('getId')->andReturn($teamId);

        $team->shouldReceive('addPlayer')
            ->once()
            ->andThrow(new TeamPlayerLimitExceededException(11));

        $teamRepository->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($id) => $id->getValue() === $teamId->getValue()))
            ->andReturn($team);

        // Act & Assert
        $this->expectException(TeamPlayerLimitExceededException::class);
        $handler($command);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
