<?php

namespace App\Application\Handler;

use App\Application\Command\UpdateTeamCommand;
use App\Application\Dto\TeamDto;
use App\Application\Dto\TeamPlayerDto;
use App\Domain\Exception\InvalidTeamYearFoundedException;
use App\Domain\Exception\InvalidUlidException;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

readonly class UpdateTeamHandler implements CommandHandlerInterface
{

    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    /**
     * @throws TeamNotFoundException
     * @throws InvalidUlidException
     * @throws InvalidTeamYearFoundedException
     */
    public function __invoke(UpdateTeamCommand $command): TeamDto
    {
        $teamId = new TeamId($command->id);
        $team = $this->teamRepository->findById($teamId);
        if (!$team) {
            throw new TeamNotFoundException($teamId->getValue());
        }

        $team->update(
            name: $command->name,
            city: $command->city,
            yearFounded: $command->yearFounded,
            stadiumName: $command->stadiumName
        );

        $this->teamRepository->save($team);

        return new TeamDto(
            id: $team->getId(),
            name: $team->getName(),
            city: $team->getCity(),
            yearFounded: $team->getYearFounded(),
            stadiumName: $team->getStadiumName(),
            players: array_map(fn($player) => new TeamPlayerDto(
                id: $player->getId(),
                firstName: $player->getFirstName(),
                lastName: $player->getLastName(),
                age: $player->getAge(),
                position: $player->getPosition(),
            ), $team->getPlayers()->getValues())
        );
    }
}
