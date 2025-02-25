<?php

namespace App\Application\Command;

use App\Shared\Application\Command\CommandInterface;

class RemovePlayerFromTeamCommand implements CommandInterface
{
    public function __construct(
        public string $teamId,
        public string $playerId,
    )
    {
    }
}

