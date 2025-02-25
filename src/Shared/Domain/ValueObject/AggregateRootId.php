<?php

namespace App\Shared\Domain\ValueObject;

use App\Domain\Exception\InvalidUlidException;
use App\Shared\Domain\Services\UlidService;

abstract class AggregateRootId
{
    protected string $ulid;

    /**
     * @throws InvalidUlidException
     */
    public function __construct(string $ulid)
    {
        if (!UlidService::isValid($ulid)) {
            throw new InvalidUlidException($ulid);
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
