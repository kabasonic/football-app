<?php

namespace App\Domain\Exception;

use Exception;

class InvalidPlayerAgeException extends Exception
{
    public function __construct(string $age, string $minAge, string $maxAge)
    {
        parent::__construct("Not player age - $age. Age must be between $minAge and $maxAge");
    }
}
