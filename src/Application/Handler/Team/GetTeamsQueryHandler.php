<?php

namespace App\Application\Handler\Team;

use App\Application\DTO\Team\TeamDTO;
use App\Application\Query\Team\GetTeamsQuery;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;

class GetTeamsQueryHandler implements QueryHandlerInterface
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
            );
        }, $teams);
    }
}
