<?php

namespace App\Application\Handler;

use App\Application\Command\CreateTeamCommand;
use App\Application\Dto\TeamDto;
use App\Application\Dto\TeamPlayerDto;
use App\Domain\Exception\InvalidUlidException;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Domain\Services\UlidService;

readonly class CreateTeamHandler implements CommandHandlerInterface
{

    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    /**
     * @throws InvalidUlidException
     */
    public function __invoke(CreateTeamCommand $command): TeamDto
    {
        $team = Team::create(
            id: new TeamId(UlidService::generate()),
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
