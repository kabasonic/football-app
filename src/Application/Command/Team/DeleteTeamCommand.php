<?php

namespace App\Application\Command\Team;

use App\Shared\Application\Command\CommandInterface;

class DeleteTeamCommand implements CommandInterface
{
    public function __construct(
        public string $id,
    )
    {
    }
}

