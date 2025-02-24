<?php

namespace App\Application\Service;

use Psr\Log\LoggerInterface;

class NotificationService
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function sendRelocationNotification(string $playerName, string $teamName, string $newCity): void
    {
        $this->logger->info("Notification to Player {$playerName}: Your team {$teamName} has been relocated to {$newCity}.");
    }
}
