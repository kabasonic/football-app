<?php

namespace App\Tests\Application\Handler;

use App\Application\Command\RemovePlayerFromTeamCommand;
use App\Application\Handler\RemovePlayerFromTeamHandler;
use App\Domain\Exception\PlayerNotFoundException;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\Entity\Player;
use Mockery;
use PHPUnit\Framework\TestCase;

class RemovePlayerFromTeamHandlerTest extends TestCase
{
    public function testRemovePlayerFromTeamSuccessfully(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new RemovePlayerFromTeamHandler($teamRepository);

        $teamId = new TeamId('01H7ZVGN8Y45JEM3HMT8MEGMC6');
        $playerId = new PlayerId('01H8Y6V5T8MEGJEM3HMTZVGNC8');

        $command = new RemovePlayerFromTeamCommand(
            teamId: $teamId->getValue(),
            playerId: $playerId->getValue()
        );

        // Create a mock player
        $player = Mockery::mock(Player::class);
        $player->shouldReceive('getId')->andReturn($playerId);

        $team = Mockery::mock(Team::class);
        $team->shouldReceive('findPlayerById')->with(Mockery::on(function ($arg) use ($playerId) {
            return $arg instanceof PlayerId && $arg->getValue() === $playerId->getValue();
        }))->andReturn($player);
        $team->shouldReceive('removePlayer')->with($player)->andReturnSelf();

        $teamRepository->shouldReceive('findById')->with(Mockery::on(function ($arg) use ($teamId) {
            return $arg instanceof TeamId && $arg->getValue() === $teamId->getValue();
        }))->andReturn($team);

        $teamRepository->shouldReceive('removePlayer')->with($player)->once();
        $teamRepository->shouldReceive('save')->with($team)->once();

        // Act
        $handler($command);

        // Assert
        $teamRepository->shouldHaveReceived('save');
        $teamRepository->shouldHaveReceived('removePlayer');
        $this->expectNotToPerformAssertions();
    }

    public function testThrowsExceptionWhenTeamNotFound(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new RemovePlayerFromTeamHandler($teamRepository);

        $teamId = new TeamId('01H7ZVGN8Y45JEM3HMT8MEGMC6');
        $playerId = new PlayerId('01H8Y6V5T8MEGJEM3HMTZVGNC8');

        $command = new RemovePlayerFromTeamCommand(
            teamId: $teamId->getValue(),
            playerId: $playerId->getValue()
        );

        $teamRepository->shouldReceive('findById')->with(Mockery::on(function ($arg) use ($teamId) {
            return $arg instanceof TeamId && $arg->getValue() === $teamId->getValue();
        }))->andReturnNull();

        // Act & Assert
        $this->expectException(TeamNotFoundException::class);
        $handler($command);
    }

    public function testThrowsExceptionWhenPlayerNotFound(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new RemovePlayerFromTeamHandler($teamRepository);

        $teamId = new TeamId('01H7ZVGN8Y45JEM3HMT8MEGMC6');
        $playerId = new PlayerId('01H8Y6V5T8MEGJEM3HMTZVGNC8');

        $command = new RemovePlayerFromTeamCommand(
            teamId: $teamId->getValue(),
            playerId: $playerId->getValue()
        );

        $team = Mockery::mock(Team::class);
        $team->shouldReceive('findPlayerById')->with(Mockery::on(function ($arg) use ($playerId) {
            return $arg instanceof PlayerId && $arg->getValue() === $playerId->getValue();
        }))->andReturnNull();

        $teamRepository->shouldReceive('findById')->with(Mockery::on(function ($arg) use ($teamId) {
            return $arg instanceof TeamId && $arg->getValue() === $teamId->getValue();
        }))->andReturn($team);

        // Act & Assert
        $this->expectException(PlayerNotFoundException::class);
        $handler($command);
    }
}
