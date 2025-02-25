<?php

namespace App\Infrastructure\EventListener;

use App\Application\Service\NotificationService;
use App\Domain\Event\TeamRelocatedEvent;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Repository\TeamRepositoryInterface;

readonly class TeamRelocatedListener
{

    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private NotificationService $notificationService
    )
    {
    }

    /**
     * @throws TeamNotFoundException
     */
    public function handle(TeamRelocatedEvent $event): void
    {
        $teamId = $event->getTeamId();
        $newCity = $event->getNewCity();

        $team = $this->teamRepository->findById($teamId);
        if (!$team) {
            throw new TeamNotFoundException($teamId->getValue());
        }

        $players = $team->getPlayers();

        foreach ($players as $player) {
            $this->notificationService->sendRelocationNotification(
                $player->getFirstName(),
                $player->getTeam()->getName(),
                $newCity
            );
        }
    }
}
