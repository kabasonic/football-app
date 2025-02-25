<?php

namespace App\Application\Dto;

class TeamPlayerDto
{
    public function __construct(
        public string $id,
        public string $firstName,
        public string $lastName,
        public int    $age,
        public string $position,
    )
    {
    }
}
