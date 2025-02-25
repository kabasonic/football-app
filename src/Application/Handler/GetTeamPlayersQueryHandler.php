<?php

namespace App\Application\Handler;

use App\Application\Dto\PlayerDto;
use App\Application\Query\GetTeamPlayersQuery;
use App\Domain\Exception\InvalidUlidException;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;

readonly class GetTeamPlayersQueryHandler implements QueryHandlerInterface
{
    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    /**
     * @throws TeamNotFoundException
     * @throws InvalidUlidException
     */
    public function __invoke(GetTeamPlayersQuery $query): array
    {
        $team = $this->teamRepository->findById($query->getTeamId());

        if(!$team){
            throw new TeamNotFoundException($query->getTeamId());
        }

        return array_map(function ($player) use ($team) {
            return new PlayerDto(
                id: $player->getId(),
                firstName: $player->getFirstName(),
                lastName: $player->getLastName(),
                age: $player->getAge(),
                position: $player->getPosition(),
                teamId: $team->getId()
            );
        }, $team->getPlayers()->getValues());
    }
}
