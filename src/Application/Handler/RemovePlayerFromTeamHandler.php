<?php

namespace App\Application\Handler;

use App\Application\Command\RemovePlayerFromTeamCommand;
use App\Domain\Exception\InvalidUlidException;
use App\Domain\Exception\PlayerNotFoundException;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

readonly class RemovePlayerFromTeamHandler implements CommandHandlerInterface
{

    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    /**
     * @throws PlayerNotFoundException
     * @throws TeamNotFoundException
     * @throws InvalidUlidException
     */
    public function __invoke(RemovePlayerFromTeamCommand $command): void
    {
        $teamId = new TeamId($command->teamId);
        $playerId = new PlayerId($command->playerId);

        $team = $this->teamRepository->findById($teamId);
        if (!$team) {
            throw new TeamNotFoundException($teamId->getValue());
        }

        $player = $team->findPlayerById($playerId);
        if (!$player) {
            throw new PlayerNotFoundException($playerId->getValue());
        }

        $team->removePlayer($player);

        $this->teamRepository->removePlayer($player);
        $this->teamRepository->save($team);
    }
}
