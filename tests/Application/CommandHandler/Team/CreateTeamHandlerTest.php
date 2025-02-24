<?php

namespace App\Tests\Application\CommandHandler\Team;

use App\Application\Command\Team\CreateTeamCommand;
use App\Application\Handler\Team\CreateTeamHandler;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\Tools\FakerTools;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateTeamHandlerTest extends KernelTestCase
{
    use FakerTools;

    private CreateTeamHandler $createTeamHandler;
    private $teamRepositoryMock;
    private CommandBusInterface $commandBus;

    protected function setUp(): void
    {
        self::bootKernel(); // Uruchom kernel Symfony

        $this->teamRepositoryMock = $this->createMock(TeamRepositoryInterface::class);
        $this->commandBus = self::getContainer()->get(CommandBusInterface::class);

        $this->createTeamHandler = new CreateTeamHandler($this->teamRepositoryMock);
    }

    public function testCreateTeam(): void
    {
        $command = new CreateTeamCommand(
            name: $this->getFaker()->name(),
            city: $this->getFaker()->city(),
            yearFounded: $this->getFaker()->year(),
            stadiumName: $this->getFaker()->name(),
        );

        $this->teamRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Team::class));

        $this->createTeamHandler->__invoke($command);
    }
}
