<?php

namespace App\Tests\Application\CommandHandler\Team;

use App\Application\Command\Team\DeleteTeamCommand;
use App\Application\Handler\Team\DeleteTeamHandler;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Domain\Services\UlidService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DeleteTeamHandlerTest extends KernelTestCase
{
    private DeleteTeamHandler $handler;
    private $teamRepositoryMock;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->teamRepositoryMock = $this->createMock(TeamRepositoryInterface::class);
        $this->handler = new DeleteTeamHandler($this->teamRepositoryMock);
    }

    public function testDeleteTeam(): void
    {
        $teamId = new TeamId(UlidService::generate());
        $team = $this->createMock(Team::class);
        $team->method('getId')->willReturn($teamId);

        $this->teamRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with($this->callback(fn (TeamId $id) => $id->getValue() === $teamId->getValue()))
            ->willReturn($team);

        $this->teamRepositoryMock
            ->expects($this->once())
            ->method('delete')
            ->with($team);

        $command = new DeleteTeamCommand($teamId->getValue());
        $this->handler->__invoke($command);
    }

    public function testDeleteNonExistentTeam(): void
    {
        $teamId = new TeamId(UlidService::generate());

        $this->teamRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with($this->callback(fn (TeamId $id) => $id->getValue() === $teamId->getValue()))
            ->willReturn(null);

        $this->teamRepositoryMock
            ->expects($this->never())
            ->method('delete');

        $command = new DeleteTeamCommand($teamId->getValue());
        $this->handler->__invoke($command);
    }
}

