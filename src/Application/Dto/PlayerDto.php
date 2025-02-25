<?php

namespace App\Application\Dto;

class PlayerDto
{
    public function __construct(
        public string $id,
        public string $firstName,
        public string $lastName,
        public int    $age,
        public string $position,
        public string $teamId,
    )
    {
    }
}
