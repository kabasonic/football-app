<?php

namespace App\Domain\Exception;

use Exception;

class InvalidTeamYearFoundedException extends Exception
{
    public function __construct(int $yearFounded, int $minYear, int $maxYear)
    {
        parent::__construct("Invalid year founded $yearFounded. Year founded must be between $minYear and $maxYear");
    }
}
