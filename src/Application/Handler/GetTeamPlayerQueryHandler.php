<?php

namespace App\Application\Handler;

use App\Application\Dto\PlayerDto;
use App\Application\Query\GetTeamPlayerQuery;
use App\Domain\Exception\InvalidUlidException;
use App\Domain\Exception\PlayerNotFoundException;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;

readonly class GetTeamPlayerQueryHandler implements QueryHandlerInterface
{
    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    /**
     * @throws PlayerNotFoundException
     * @throws TeamNotFoundException
     * @throws InvalidUlidException
     */
    public function __invoke(GetTeamPlayerQuery $query): PlayerDto
    {
        $team = $this->teamRepository->findById($query->getTeamId());

        if (!$team) {
            throw new TeamNotFoundException($query->getTeamId());
        }

        $player = $team->findPlayerById($query->getPlayerId());

        if(!$player) {
            throw new PlayerNotFoundException($query->getPlayerId());
        }

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
