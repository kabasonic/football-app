<?php

namespace App\Application\Handler\Team;

use App\Application\Command\Team\AddPlayerToTeamCommand;
use App\Domain\Models\Team\Entity\Player;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Domain\Services\UlidService;

class AddPlayerToTeamHandler implements CommandHandlerInterface
{

    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    public function __invoke(AddPlayerToTeamCommand $command): void
    {
        $teamId = new TeamId($command->teamId);
        $team = $this->teamRepository->findById($teamId);

        if (!$team) {
            throw new \DomainException('Team not found');
        }

        $player = new Player(
            id: new PlayerId(UlidService::generate()),
            firstName: $command->firstName,
            lastName: $command->lastName,
            age: $command->age,
            position: $command->position
        );

        $team->addPlayer($player);

        $this->teamRepository->savePlayer($team, $player);
    }
}
