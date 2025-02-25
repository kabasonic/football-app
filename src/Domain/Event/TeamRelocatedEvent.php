<?php

namespace App\Domain\Event;

use App\Domain\Models\Team\ValueObject\TeamId;
use App\Shared\Domain\Event\DomainEventInterface;
use Symfony\Contracts\EventDispatcher\Event;

class TeamRelocatedEvent extends Event implements DomainEventInterface
{
    private TeamId $teamId;
    private string $newCity;

    public function __construct(TeamId $teamId, string $newCity)
    {
        $this->teamId = $teamId;
        $this->newCity = $newCity;
    }

    public function getTeamId(): TeamId
    {
        return $this->teamId;
    }

    public function getNewCity(): string
    {
        return $this->newCity;
    }

}
