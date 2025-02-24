<?php

namespace App\Application\Handler\Team;

use App\Application\DTO\Team\TeamDTO;
use App\Application\Query\Team\GetTeamQuery;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;

class GetTeamQueryHandler implements QueryHandlerInterface
{
    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    public function __invoke(GetTeamQuery $query): TeamDTO
    {
        $team = $this->teamRepository->findById($query->getId());

        return new TeamDto(
            id: $team->getId(),
            name: $team->getName(),
            city: $team->getCity(),
            yearFounded: $team->getYearFounded(),
            stadiumName: $team->getStadiumName(),
        );
    }
}
