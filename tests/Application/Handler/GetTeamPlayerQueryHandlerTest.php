<?php

declare(strict_types=1);

namespace App\Tests\Application\Handler;

use App\Application\Dto\PlayerDto;
use App\Application\Handler\GetTeamPlayerQueryHandler;
use App\Application\Query\GetTeamPlayerQuery;
use App\Domain\Exception\PlayerNotFoundException;
use App\Domain\Models\Team\Entity\Player;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetTeamPlayerQueryHandlerTest extends TestCase
{
    public function testGetTeamPlayerSuccessfully(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new GetTeamPlayerQueryHandler($teamRepository);

        $teamId = new TeamId('01H7ZVGN8Y45JEM3HMT8MEGMC6');
        $playerId = new PlayerId('01H8Y6V5T8MEGJEM3HMTZVGNC8');

        $query = new GetTeamPlayerQuery($teamId->getValue(), $playerId->getValue());

        $player = Mockery::mock(Player::class);
        $player->shouldReceive('getId')->andReturn($playerId);
        $player->shouldReceive('getFirstName')->andReturn('John');
        $player->shouldReceive('getLastName')->andReturn('Doe');
        $player->shouldReceive('getAge')->andReturn(25);
        $player->shouldReceive('getPosition')->andReturn('Forward');

        $team = Mockery::mock(Team::class);
        $team->shouldReceive('findPlayerById')->with(Mockery::on(function ($arg) use ($playerId) {
            return $arg instanceof PlayerId;
        }))->andReturn($player);

        $team->shouldReceive('getId')->andReturn($teamId);

        $teamRepository->shouldReceive('findById')->with(Mockery::on(function ($arg) use ($teamId) {
            return $arg instanceof TeamId;
        }))->andReturn($team);

        // Act
        $result = $handler($query);

        // Assert
        $this->assertInstanceOf(PlayerDto::class, $result);
        $this->assertEquals('John', $result->firstName);
        $this->assertEquals('Doe', $result->lastName);
        $this->assertEquals(25, $result->age);
        $this->assertEquals('Forward', $result->position);
        $this->assertEquals($teamId, $result->teamId);
    }

    public function testThrowsExceptionWhenPlayerNotFound(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new GetTeamPlayerQueryHandler($teamRepository);

        $teamId = new TeamId('01H7ZVGN8Y45JEM3HMT8MEGMC6');
        $playerId = new PlayerId('01H8Y6V5T8MEGJEM3HMTZVGNC8');

        $query = new GetTeamPlayerQuery($teamId->getValue(), $playerId->getValue());

        $team = Mockery::mock(Team::class);
        $team->shouldReceive('findPlayerById')->with(Mockery::on(function ($arg) use ($playerId) {
            return $arg instanceof PlayerId;
        }))->andReturn(null);
        $team->shouldReceive('getId')->andReturn($teamId);

        $teamRepository->shouldReceive('findById')->with(Mockery::on(function ($arg) use ($teamId) {
            return $arg instanceof TeamId;
        }))->andReturn($team);

        // Act & Assert
        $this->expectException(PlayerNotFoundException::class);
        $handler($query);
    }


    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
