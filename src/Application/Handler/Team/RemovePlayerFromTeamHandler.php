<?php

namespace App\Application\Handler\Team;

use App\Application\Command\Team\RemovePlayerFromTeamCommand;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

class RemovePlayerFromTeamHandler implements CommandHandlerInterface
{

    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    public function __invoke(RemovePlayerFromTeamCommand $command): void
    {
        $teamId = new TeamId($command->teamId);
        $playerId = new PlayerId($command->playerId);

        $player = $this->teamRepository->findPlayerByTeam($teamId, $playerId);

        if($player){
            $this->teamRepository->removePlayer($player);
        }
    }
}
