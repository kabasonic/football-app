<?php

namespace App\Application\Query;

use App\Domain\Exception\InvalidUlidException;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Shared\Application\Query\QueryInterface;

class GetTeamPlayerQuery implements QueryInterface
{
    private string $teamId;
    private string $playerId;

    public function __construct(string $teamId, string $playerId)
    {
        $this->teamId = $teamId;
        $this->playerId = $playerId;
    }

    /**
     * @throws InvalidUlidException
     */
    public function getTeamId(): TeamId
    {
        return new TeamId($this->teamId);
    }

    /**
     * @throws InvalidUlidException
     */
    public function getPlayerId(): PlayerId
    {
        return new PlayerId($this->playerId);
    }
}
