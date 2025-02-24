<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Services\UlidService;

abstract class AggregateRootId
{
    protected string $ulid;

    public function __construct(string $ulid)
    {
        if (!UlidService::isValid($ulid)) {
            throw new \InvalidArgumentException('Not valid ULID');
        }

        $this->ulid = $ulid;
    }

    public function getValue(): string
    {
        return $this->ulid;
    }

    public function __toString(): string
    {
        return $this->ulid;
    }
}
