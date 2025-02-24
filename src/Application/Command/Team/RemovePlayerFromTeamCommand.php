<?php

namespace App\Application\Command\Team;

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

