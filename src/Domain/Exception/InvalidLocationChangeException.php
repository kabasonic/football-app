<?php

namespace App\Domain\Exception;

use Exception;

class InvalidLocationChangeException extends Exception
{
    public function __construct(string $oldLocation, string $newLocation)
    {
        parent::__construct("New location $newLocation must be different from the current $oldLocation.");
    }
}
