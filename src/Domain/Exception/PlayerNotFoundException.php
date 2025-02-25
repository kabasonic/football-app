<?php

namespace App\Domain\Exception;

use Exception;

class PlayerNotFoundException extends Exception
{
    public function __construct(string $playerId)
    {
        parent::__construct("Player with ID $playerId not found.");
    }
}
