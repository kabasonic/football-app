<?php

namespace App\Application\Command;

use App\Shared\Application\Command\CommandInterface;

class DeleteTeamCommand implements CommandInterface
{
    public function __construct(
        public string $id,
    )
    {
    }
}

