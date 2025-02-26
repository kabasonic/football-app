<?php

namespace App\Application\Handler;

use App\Application\Dto\TeamDto;
use App\Application\Dto\TeamPlayerDto;
use App\Application\Query\GetTeamQuery;
use App\Domain\Exception\InvalidUlidException;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;

readonly class GetTeamQueryHandler implements QueryHandlerInterface
{
    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    /**
     * @throws InvalidUlidException
     * @throws TeamNotFoundException
     */
    public function __invoke(GetTeamQuery $query): TeamDto
    {
        $team = $this->teamRepository->findById($query->getId());
        if(!$team) {
            throw new TeamNotFoundException($query->getId());
        }

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
