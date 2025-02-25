<?php

namespace App\Application\Handler;

use App\Application\Command\RelocateTeamCommand;
use App\Application\Dto\TeamDto;
use App\Application\Dto\TeamPlayerDto;
use App\Domain\Exception\InvalidLocationChangeException;
use App\Domain\Exception\InvalidUlidException;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;


readonly class RelocateTeamHandler implements CommandHandlerInterface
{

    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    /**
     * @throws TeamNotFoundException
     * @throws InvalidUlidException
     * @throws InvalidLocationChangeException
     */
    public function __invoke(RelocateTeamCommand $command): TeamDto
    {
        $teamId = new TeamId($command->teamId);
        $team = $this->teamRepository->findById($teamId);
        if (!$team) {
            throw new TeamNotFoundException($teamId);
        }

        $team->relocate($command->city);

        $this->teamRepository->save($team);

        foreach ($team->pullDomainEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
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
