<?php

namespace App\Domain\Repository;

use App\Domain\Models\Team\Entity\Player;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;

interface TeamRepositoryInterface
{
    public function save(Team $team): void;

    public function findById(TeamId $id): ?Team;

    public function findPLayersByTeamId(TeamId $teamId): array;

    public function delete(Team $team): void;

    public function savePlayer(Team $team, Player $player): void;

    public function findPlayerByTeam(TeamId $teamId, PlayerId $playerId): ?Player;

    public function removePlayer(Player $player): void;
}
