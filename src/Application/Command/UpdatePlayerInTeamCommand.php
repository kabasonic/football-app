<?php

namespace App\Application\Command;

use App\Shared\Application\Command\CommandInterface;

class UpdatePlayerInTeamCommand implements CommandInterface
{
    public function __construct(
        public string $teamId,
        public string $playerId,
        public string $firstName,
        public string $lastName,
        public int    $age,
        public string $position,
    )
    {
    }
}

