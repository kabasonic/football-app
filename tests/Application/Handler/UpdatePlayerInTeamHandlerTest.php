<?php

namespace App\Tests\Application\Handler;

use App\Application\Command\UpdatePlayerInTeamCommand;
use App\Application\Handler\UpdatePlayerInTeamHandler;
use App\Domain\Exception\PlayerNotFoundException;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\Entity\Player;
use App\Application\Dto\PlayerDto;
use Mockery;
use PHPUnit\Framework\TestCase;

class UpdatePlayerInTeamHandlerTest extends TestCase
{
    public function testUpdatePlayerInTeamSuccessfully(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new UpdatePlayerInTeamHandler($teamRepository);

        $teamId = new TeamId('01H7ZVGN8Y45JEM3HMT8MEGMC6');
        $playerId = new PlayerId('01H8Y6V5T8MEGJEM3HMTZVGNC8');

        $command = new UpdatePlayerInTeamCommand(
            teamId: $teamId->getValue(),
            playerId: $playerId->getValue(),
            firstName: 'John',
            lastName: 'Doe',
            age: 26,
            position: 'Midfielder'
        );

        $player = Mockery::mock(Player::class);
        $player->shouldReceive('getId')->andReturn($playerId);
        $player->shouldReceive('getFirstName')->andReturn('John');
        $player->shouldReceive('getLastName')->andReturn('Doe');
        $player->shouldReceive('getAge')->andReturn(26);
        $player->shouldReceive('getPosition')->andReturn('Forward');
        $player->shouldReceive('setFirstName')->with('John')->andReturnSelf();
        $player->shouldReceive('setLastName')->with('Doe')->andReturnSelf();
        $player->shouldReceive('setAge')->with(26)->andReturnSelf();
        $player->shouldReceive('setPosition')->with('Midfielder')->andReturnSelf();

        $team = Mockery::mock(Team::class);
        $team->shouldReceive('getId')->andReturn($teamId);
        $team->shouldReceive('findPlayerById')->with(Mockery::on(function ($arg) use ($playerId) {
            return $arg instanceof PlayerId && $arg->getValue() === $playerId->getValue();
        }))->andReturn($player);

        $team->shouldReceive('updatePlayer')
            ->with(
                Mockery::on(function ($arg) use ($playerId) {
                    return $arg instanceof PlayerId && $arg->getValue() === $playerId->getValue();
                }),
                'John',
                'Doe',
                26,
                'Midfielder'
            )
            ->andReturnSelf();

        $teamRepository->shouldReceive('findById')->with(Mockery::on(function ($arg) use ($teamId) {
            return $arg instanceof TeamId && $arg->getValue() === $teamId->getValue();
        }))->andReturn($team);

        $teamRepository->shouldReceive('save')->with($team)->once();

        // Act
        $result = $handler($command);

        // Assert
        $this->assertInstanceOf(PlayerDto::class, $result);
        $this->assertEquals('John', $result->firstName);
        $this->assertEquals('Doe', $result->lastName);
        $this->assertEquals(26, $result->age);
        $this->assertEquals('Forward', $result->position);
        $this->assertEquals($teamId, $result->teamId);
    }

    public function testThrowsExceptionWhenTeamNotFound(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new UpdatePlayerInTeamHandler($teamRepository);

        $teamId = new TeamId('01H7ZVGN8Y45JEM3HMT8MEGMC6');
        $playerId = new PlayerId('01H8Y6V5T8MEGJEM3HMTZVGNC8');

        $command = new UpdatePlayerInTeamCommand(
            teamId: $teamId->getValue(),
            playerId: $playerId->getValue(),
            firstName: 'John',
            lastName: 'Doe',
            age: 26,
            position: 'Midfielder'
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
        $handler = new UpdatePlayerInTeamHandler($teamRepository);

        $teamId = new TeamId('01H7ZVGN8Y45JEM3HMT8MEGMC6');
        $playerId = new PlayerId('01H8Y6V5T8MEGJEM3HMTZVGNC8');

        $command = new UpdatePlayerInTeamCommand(
            teamId: $teamId->getValue(),
            playerId: $playerId->getValue(),
            firstName: 'John',
            lastName: 'Doe',
            age: 26,
            position: 'Midfielder'
        );

        $team = Mockery::mock(Team::class);
        $team->shouldReceive('getId')->andReturn($teamId);
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
