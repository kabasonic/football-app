<?php

namespace App\Tests\Resource\Fixtures;

use App\Domain\Exception\InvalidUlidException;
use App\Domain\Exception\TeamPlayerLimitExceededException;
use App\Domain\Models\Team\Entity\Player;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Shared\Domain\Services\UlidService;
use App\Tests\Tools\FakerTools;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlayerFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTools;

    public const PLAYER_1_REFERENCE = 'player-1';

    /**
     * @throws InvalidUlidException
     * @throws TeamPlayerLimitExceededException
     */
    public function load(ObjectManager $manager): void
    {
        /** @var Team $team */
        $team = $this->getReference(TeamFixtures::TEAM_1_REFERENCE, Team::class);

        for ($i = 1; $i <= 11; $i++) {
            $player = new Player(
                new PlayerId(UlidService::generate()),
                $this->getFaker()->firstName(),
                $this->getFaker()->lastName(),
                $this->getFaker()->numberBetween(12,50),
                'FORWARD'
            );

            $team->addPlayer($player);
            $manager->persist($player);

            // Dodaj referencję do pierwszego gracza
            if ($i === 1) {
                $this->addReference(self::PLAYER_1_REFERENCE, $player);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [TeamFixtures::class];
    }
}
