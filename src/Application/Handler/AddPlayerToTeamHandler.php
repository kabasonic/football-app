<?php

namespace App\Application\Handler;

use App\Application\Command\AddPlayerToTeamCommand;
use App\Application\Dto\PlayerDto;
use App\Domain\Exception\InvalidPlayerAgeException;
use App\Domain\Exception\InvalidPlayerPositionException;
use App\Domain\Exception\InvalidUlidException;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Exception\TeamPlayerLimitExceededException;
use App\Domain\Models\Team\Entity\Player;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Domain\Services\UlidService;

readonly class AddPlayerToTeamHandler implements CommandHandlerInterface
{

    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    /**
     * @param AddPlayerToTeamCommand $command
     * @return PlayerDto
     * @throws InvalidUlidException
     * @throws TeamNotFoundException
     * @throws TeamPlayerLimitExceededException
     * @throws InvalidPlayerAgeException
     * @throws InvalidPlayerPositionException
     */
    public function __invoke(AddPlayerToTeamCommand $command): PlayerDto
    {
        $teamId = new TeamId($command->teamId);
        $team = $this->teamRepository->findById($teamId);

        if (!$team) {
            throw new TeamNotFoundException($teamId->getValue());
        }

        $player = new Player(
            id: new PlayerId(UlidService::generate()),
            firstName: $command->firstName,
            lastName: $command->lastName,
            age: $command->age,
            position: $command->position
        );

        $team->addPlayer($player);

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
