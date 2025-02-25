<?php

namespace App\Application\Command;

use App\Shared\Application\Command\CommandInterface;

class CreateTeamCommand implements CommandInterface
{
    public function __construct(
        public string $name,
        public string $city,
        public int $yearFounded,
        public string $stadiumName
    )
    {
    }
}

