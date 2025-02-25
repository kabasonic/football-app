<?php

namespace App\Application\Query;

use App\Domain\Exception\InvalidUlidException;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Shared\Application\Query\QueryInterface;

class GetTeamPlayersQuery implements QueryInterface
{
    private string $teamId;

    public function __construct(string $teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * @throws InvalidUlidException
     */
    public function getTeamId(): TeamId
    {
        return new TeamId($this->teamId);
    }
}
