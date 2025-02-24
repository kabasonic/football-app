<?php

namespace App\Application\Handler\Team;

use App\Application\Command\Team\UpdateTeamCommand;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

class UpdateTeamHandler implements CommandHandlerInterface
{

    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    public function __invoke(UpdateTeamCommand $command): void
    {
        $teamId = new TeamId($command->id);
        $team = $this->teamRepository->findById($teamId);
        if (!$team) {
            throw new \DomainException("Team not found");
        }

        $team->update(
            name: $command->name,
            city: $command->city,
            yearFounded: $command->yearFounded,
            stadiumName: $command->stadiumName
        );

        $this->teamRepository->save($team);
    }
}
