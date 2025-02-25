<?php

namespace App\Domain\Exception;

use Exception;

class TeamPlayerLimitExceededException extends Exception
{
    public function __construct(int $maxPlayersCount)
    {
        parent::__construct("A team cannot have more than $maxPlayersCount players.");
    }
}
