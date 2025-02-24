<?php

namespace App\Tests\Resource\Fixtures;

use App\Domain\Models\Team\Entity\Player;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Shared\Domain\Services\UlidService;
use App\Tests\Tools\FakerTools;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TeamFixtures extends Fixture
{
    use FakerTools;

    public const TEAM_1_REFERENCE = 'team-1';
    public const TEAM_2_REFERENCE = 'team-2';
    public const TEAM_3_REFERENCE = 'team-3';

    use FakerTools;

    public function load(ObjectManager $manager): void
    {
        $team1 = new Team(
            id: new TeamId(UlidService::generate()),
            name: 'Team 1',
            city: $this->getFaker()->city(),
            yearFounded: $this->getFaker()->year(),
            stadiumName: $this->getFaker()->company()
        );

        for ($j = 1; $j <= 11; $j++) {
            $playerId = new PlayerId(UlidService::generate());
            $player = new Player(
                $playerId,
                'Player ' . $j,
                'LastName ' . $j,
                20 + $j,
                'Position ' . $j
            );

            $team1->addPlayer($player);
            $manager->persist($player);
        }

        $manager->persist($team1);
        $this->addReference(self::TEAM_1_REFERENCE, $team1);

        // Tworzymy drużynę 2
        $team2 = new Team(
            id: new TeamId(UlidService::generate()),
            name: 'Team 2',
            city: $this->getFaker()->city(),
            yearFounded: $this->getFaker()->year(),
            stadiumName: $this->getFaker()->company()
        );

        for ($j = 1; $j <= 11; $j++) {
            $playerId = new PlayerId(UlidService::generate());
            $player = new Player(
                $playerId,
                'Player ' . $j,
                'LastName ' . $j,
                20 + $j,
                'Position ' . $j
            );

            $team2->addPlayer($player);
            $manager->persist($player);
        }

        $manager->persist($team2);
        $this->addReference(self::TEAM_2_REFERENCE, $team2);

        $team3 = new Team(
            id: new TeamId(UlidService::generate()),
            name: 'Team 3',
            city: $this->getFaker()->city(),
            yearFounded: $this->getFaker()->year(),
            stadiumName: $this->getFaker()->company()
        );

        for ($j = 1; $j <= 11; $j++) {
            $playerId = new PlayerId(UlidService::generate());
            $player = new Player(
                $playerId,
                'Player ' . $j,
                'LastName ' . $j,
                20 + $j,
                'Position ' . $j
            );

            $team3->addPlayer($player);
            $manager->persist($player);
        }

        $manager->persist($team3);
        $this->addReference(self::TEAM_3_REFERENCE, $team3);

        $manager->flush();
    }
}
