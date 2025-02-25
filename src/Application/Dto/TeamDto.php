<?php

namespace App\Application\Dto;

class TeamDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $city,
        public int    $yearFounded,
        public string $stadiumName,
        public array  $players = [],
    )
    {
    }
}
