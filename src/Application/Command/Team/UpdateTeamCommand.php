<?php

namespace App\Application\Command\Team;

use App\Shared\Application\Command\CommandInterface;

class UpdateTeamCommand implements CommandInterface
{
    public function __construct(
        public string $id,
        public string $name,
        public string $city,
        public int $yearFounded,
        public string $stadiumName
    )
    {
    }
}

