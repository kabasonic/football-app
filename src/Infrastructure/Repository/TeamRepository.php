<?php

namespace App\Infrastructure\Repository;

use App\Domain\Models\Team\Entity\Player;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

class TeamRepository extends ServiceEntityRepository implements TeamRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function save(Team $team): void
    {
        $this->getEntityManager()->persist($team);
        $this->getEntityManager()->flush();
    }

    public function findById(TeamId $id): ?Team
    {
        return $this->find($id->getValue());
    }

    public function delete(Team $team): void
    {
        $this->getEntityManager()->remove($team);
    }

    public function savePlayer(Team $team, Player $player): void
    {
        $this->getEntityManager()->persist($player);
        $this->getEntityManager()->flush();
    }


    public function findPlayerByTeam(TeamId $teamId, PlayerId $playerId): ?Player
    {
        $params = new ArrayCollection([
            new Parameter('teamId', $teamId->getValue()),
            new Parameter('playerId', $playerId->getValue())
        ]);

        return $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from(Player::class, 'p')
            ->innerJoin('p.team', 't')
            ->where('t.id = :teamId')
            ->andWhere('p.id = :playerId')
            ->setParameters($params)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function removePlayer(Player $player): void
    {
        $this->getEntityManager()->remove($player);
        $this->getEntityManager()->flush();
    }

    public function findPLayersByTeamId(TeamId $teamId): array
    {
        $params = new ArrayCollection([
            new Parameter('teamId', $teamId->getValue()),
        ]);

        return $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from(Player::class, 'p')
            ->innerJoin('p.team', 't')
            ->where('t.id = :teamId')
            ->setParameters($params)
            ->getQuery()
            ->getResult();
    }
}
