<?php

declare(strict_types=1);

namespace App\Tests\Application\Handler;

use App\Application\Command\CreateTeamCommand;
use App\Application\Dto\TeamDto;
use App\Application\Handler\CreateTeamHandler;
use App\Domain\Repository\TeamRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class CreateTeamHandlerTest extends TestCase
{
    public function testHandleCreatesAndSavesTeam(): void
    {
        // Arrange
        $teamRepository = Mockery::mock(TeamRepositoryInterface::class);
        $handler = new CreateTeamHandler($teamRepository);

        $command = new CreateTeamCommand(
            name: 'Test Team',
            city: 'Test City',
            yearFounded: 2000,
            stadiumName: 'Test Stadium'
        );

        $teamRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn($savedTeam) => $savedTeam->getName() === 'Test Team'));

        // Act
        $result = $handler($command);

        // Assert
        $this->assertInstanceOf(TeamDto::class, $result);
        $this->assertEquals($command->name, $result->name);
        $this->assertEquals($command->city, $result->city);
        $this->assertEquals($command->yearFounded, $result->yearFounded);
        $this->assertEquals($command->stadiumName, $result->stadiumName);
        $this->assertEmpty($result->players);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
