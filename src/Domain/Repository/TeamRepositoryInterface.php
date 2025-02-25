<?php

namespace App\Domain\Repository;

use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\TeamId;

interface TeamRepositoryInterface
{
    public function save(Team $team): void;

    public function findById(TeamId $id): ?Team;

    public function delete(Team $team): void;
}
