<?php

namespace App\Application\Command\Team;

use App\Shared\Application\Command\CommandInterface;

class RelocateTeamCommand implements CommandInterface
{
    public function __construct(
        public string $teamId,
        public string $newCityName,
    )
    {
    }
}

