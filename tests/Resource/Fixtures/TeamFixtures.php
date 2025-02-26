<?php

namespace App\Tests\Resource\Fixtures;

use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Shared\Domain\Services\UlidService;
use App\Tests\Tools\FakerTools;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TeamFixtures extends Fixture implements FixtureInterface
{
    use FakerTools;

    public const TEAM_1_REFERENCE = 'team-1';
    public const TEAM_2_REFERENCE = 'team-2';
    public const TEAM_3_REFERENCE = 'team-3';

    public function load(ObjectManager $manager): void
    {
        $teams = [
            self::TEAM_1_REFERENCE => 'Team 1',
            self::TEAM_2_REFERENCE => 'Team 2',
            self::TEAM_3_REFERENCE => 'Team 3',
        ];

        foreach ($teams as $reference => $name) {
            $team = Team::create(
                id: new TeamId(UlidService::generate()),
                name: $name,
                city: $this->getFaker()->city(),
                yearFounded: $this->getFaker()->year(),
                stadiumName: $this->getFaker()->company()
            );

            $manager->persist($team);
            $this->addReference($reference, $team);
        }

        $manager->flush();
    }
}
