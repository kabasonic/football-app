<?php

declare(strict_types=1);

namespace App\Tests\Application\Handler;

use App\Application\Dto\PlayerDto;
use App\Application\Handler\GetTeamPlayersQueryHandler;
use App\Application\Query\GetTeamPlayersQuery;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Models\Team\Entity\Player;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetTeamPlayersQueryHandlerTest extends TestCase
{
    public function testGetTeamPlayersSuccessfully(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new GetTeamPlayersQueryHandler($teamRepository);

        $teamId = new TeamId('01H7ZVGN8Y45JEM3HMT8MEGMC6');

        $player1 = Mockery::mock(Player::class);
        $player1->shouldReceive('getId')->andReturn(new PlayerId('01H8Y6V5T8MEGJEM3HMTZVGNC8'));
        $player1->shouldReceive('getFirstName')->andReturn('John');
        $player1->shouldReceive('getLastName')->andReturn('Doe');
        $player1->shouldReceive('getAge')->andReturn(25);
        $player1->shouldReceive('getPosition')->andReturn('Forward');

        $player2 = Mockery::mock(Player::class);
        $player2->shouldReceive('getId')->andReturn(new PlayerId('01H8Y6V5T8MEGJEM3HMTZVGNC9'));
        $player2->shouldReceive('getFirstName')->andReturn('Jane');
        $player2->shouldReceive('getLastName')->andReturn('Smith');
        $player2->shouldReceive('getAge')->andReturn(22);
        $player2->shouldReceive('getPosition')->andReturn('Midfielder');

        $team = Mockery::mock(Team::class);
        $team->shouldReceive('getPlayers')->andReturn(new ArrayCollection([$player1, $player2]));
        $team->shouldReceive('getId')->andReturn($teamId);

        $teamRepository->shouldReceive('findById')->with(Mockery::on(function ($arg) use ($teamId) {
            return $arg instanceof TeamId; // Use equals to compare
        }))->andReturn($team);

        // Act
        $query = new GetTeamPlayersQuery($teamId->getValue());
        $result = $handler($query);

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(PlayerDto::class, $result[0]);
        $this->assertInstanceOf(PlayerDto::class, $result[1]);
    }


    public function testThrowsExceptionWhenTeamNotFound(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new GetTeamPlayersQueryHandler($teamRepository);

        $teamId = new TeamId('01H7ZVGN8Y45JEM3HMT8MEGMC6');
        $query = new GetTeamPlayersQuery($teamId->getValue());

        $teamRepository->shouldReceive('findById')->with($teamId->getValue())->andReturn(null);

        // Act & Assert
        $this->expectException(TeamNotFoundException::class);
        $handler($query);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
