<?php

namespace App\Application\Handler\Team;

use App\Application\Command\Team\CreateTeamCommand;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Domain\Services\UlidService;

class CreateTeamHandler implements CommandHandlerInterface
{

    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    public function __invoke(CreateTeamCommand $command): void
    {
        $team = Team::create(
            id: new TeamId(UlidService::generate()),
            name: $command->name,
            city: $command->city,
            yearFounded: $command->yearFounded,
            stadiumName: $command->stadiumName
        );

        $this->teamRepository->save($team);
    }
}
