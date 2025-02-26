<?php

namespace App\Domain\Exception;

use Exception;

class InvalidPlayerPositionException extends Exception
{
    public function __construct(string $position, array $playerPositions)
    {
        parent::__construct("Invalid player position $position. Must be one of: " . implode(', ', $playerPositions));
    }
}
