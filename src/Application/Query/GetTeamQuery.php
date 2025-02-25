<?php

namespace App\Application\Query;

use App\Domain\Exception\InvalidUlidException;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Shared\Application\Query\QueryInterface;

class GetTeamQuery implements QueryInterface
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @throws InvalidUlidException
     */
    public function getId(): TeamId
    {
        return new TeamId($this->id);
    }
}
