<?php

namespace App\Application\Handler;

use App\Application\Dto\TeamDto;
use App\Application\Dto\TeamPlayerDto;
use App\Application\Query\GetTeamsQuery;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;

readonly class GetTeamsQueryHandler implements QueryHandlerInterface
{
    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    public function __invoke(GetTeamsQuery $query): array
    {
        $teams = $this->teamRepository->findAll();

        return array_map(function ($team) {
            return new TeamDto(
                id: $team->getId(),
                name: $team->getName(),
                city: $team->getCity(),
                yearFounded: $team->getYearFounded(),
                stadiumName: $team->getStadiumName(),
                players: array_map(fn ($player) => new TeamPlayerDto(
                    id: $player->getId(),
                    firstName: $player->getFirstName(),
                    lastName: $player->getLastName(),
                    age: $player->getAge(),
                    position: $player->getPosition(),
                ), $team->getPlayers()->getValues())
            );
        }, $teams);
    }
}
