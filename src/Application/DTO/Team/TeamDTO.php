<?php

namespace App\Application\DTO\Team;

class TeamDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $city,
        public int    $yearFounded,
        public string $stadiumName,
    )
    {
    }
}
