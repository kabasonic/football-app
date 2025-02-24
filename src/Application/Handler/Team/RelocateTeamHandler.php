<?php

namespace App\Application\Handler\Team;

use App\Application\Command\Team\RelocateTeamCommand;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;


class RelocateTeamHandler implements CommandHandlerInterface
{

    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function __invoke(RelocateTeamCommand $command): void
    {
        $teamId = new TeamId($command->teamId);
        $team = $this->teamRepository->findById($teamId);
        if (!$team) {
            throw new \DomainException('Team not found.');
        }

        $team->relocate($command->newCityName);

        $this->teamRepository->save($team);

        foreach ($team->pullDomainEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }
    }
}
