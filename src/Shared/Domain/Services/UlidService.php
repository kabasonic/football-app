<?php

namespace App\Shared\Domain\Services;

use Symfony\Component\Uid\Ulid;

class UlidService
{
    public static function generate(): string
    {
        return Ulid::generate();
    }

    public static function isValid(string $ulid): bool
    {
        return Ulid::isValid($ulid);
    }
}

