<?php

namespace App\Domain\Exception;

use Exception;

class InvalidUlidException extends Exception
{
    public function __construct(string $ulid)
    {
        parent::__construct("Not valid ULID: $ulid");
    }
}
