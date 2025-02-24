<?php

namespace App\Application\Handler\Team;

use App\Application\Command\Team\DeleteTeamCommand;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

class DeleteTeamHandler implements CommandHandlerInterface
{

    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    public function __invoke(DeleteTeamCommand $command): void
    {
        $teamId = new TeamId($command->id);
        $team = $this->teamRepository->findById($teamId);

        if (!$team) {
            throw new \DomainException('Team not found');
        }

        $this->teamRepository->delete($team);
    }
}
