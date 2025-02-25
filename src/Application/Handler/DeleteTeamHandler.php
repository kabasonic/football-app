<?php

namespace App\Application\Handler;

use App\Application\Command\DeleteTeamCommand;
use App\Domain\Exception\InvalidUlidException;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

readonly class DeleteTeamHandler implements CommandHandlerInterface
{

    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    /**
     * @throws TeamNotFoundException
     * @throws InvalidUlidException
     */
    public function __invoke(DeleteTeamCommand $command): void
    {
        $teamId = new TeamId($command->id);
        $team = $this->teamRepository->findById($teamId);

        if (!$team) {
            throw new TeamNotFoundException($teamId->getValue());
        }

        $this->teamRepository->delete($team);
    }
}
