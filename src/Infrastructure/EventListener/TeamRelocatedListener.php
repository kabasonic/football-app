<?php

namespace App\Infrastructure\EventListener;

use App\Application\Service\NotificationService;
use App\Domain\Event\TeamRelocatedEvent;
use App\Domain\Repository\TeamRepositoryInterface;

class TeamRelocatedListener
{

    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private NotificationService $notificationService
    )
    {
    }

    public function handle(TeamRelocatedEvent $event): void
    {
        $teamId = $event->getTeamId();
        $newCity = $event->getNewCity();

        $players = $this->teamRepository->findPLayersByTeamId($teamId);

        foreach ($players as $player) {
            $this->notificationService->sendRelocationNotification(
                $player->getFirstName(),
                $player->getTeam()->getName(),
                $newCity
            );
        }
    }
}
