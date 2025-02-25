<?php

namespace App\Application\Handler;

use App\Application\Command\UpdatePlayerInTeamCommand;
use App\Application\Dto\PlayerDto;
use App\Domain\Exception\InvalidUlidException;
use App\Domain\Exception\PlayerNotFoundException;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

readonly class UpdatePlayerInTeamHandler implements CommandHandlerInterface
{

    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    /**
     * @throws TeamNotFoundException
     * @throws PlayerNotFoundException
     * @throws InvalidUlidException
     */
    public function __invoke(UpdatePlayerInTeamCommand $command): PlayerDto
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

        $team->updatePlayer(
            playerId: $playerId,
            firstName: $command->firstName,
            lastName: $command->lastName,
            age: $command->age,
            position: $command->position
        );

        $this->teamRepository->save($team);

        return new PlayerDto(
            id: $player->getId(),
            firstName: $player->getFirstName(),
            lastName: $player->getLastName(),
            age: $player->getAge(),
            position: $player->getPosition(),
            teamId: $team->getId()
        );
    }
}
