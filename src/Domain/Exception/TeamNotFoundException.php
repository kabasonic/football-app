<?php

namespace App\Domain\Exception;

use Exception;

class TeamNotFoundException extends Exception
{
    public function __construct(string $teamId)
    {
        parent::__construct("Team with ID $teamId not found.");
    }
}
