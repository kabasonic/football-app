<?php

namespace App\Tests\Domain\Repository;

use App\Domain\Models\Team\Entity\Player;
use App\Domain\Models\Team\Entity\Team;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Shared\Domain\Services\UlidService;
use App\Tests\Resource\Fixtures\PlayerFixtures;
use App\Tests\Resource\Fixtures\TeamFixtures;
use App\Tests\Tools\FakerTools;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TeamRepositoryTest extends KernelTestCase
{

    use FakerTools;

    private TeamRepositoryInterface $teamRepository;
    private AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teamRepository = self::getContainer()->get(TeamRepositoryInterface::class);
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        StaticDriver::setKeepStaticConnections(false);
    }

    public function testSaveTeam(): void
    {
        $team = Team::create(
            id: new TeamId(UlidService::generate()),
            name: $this->getFaker()->name(),
            city: $this->getFaker()->city(),
            yearFounded: $this->getFaker()->year(),
            stadiumName: $this->getFaker()->name(),
        );
        $this->teamRepository->save($team);

        $foundTeam = $this->teamRepository->findById($team->getId());
        $this->assertNotNull($foundTeam);
        $this->assertEquals($team->getId(), $foundTeam->getId());
    }

    public function testFindById(): void
    {
        $executor = $this->databaseTool->loadFixtures([TeamFixtures::class]);
        $team = $executor->getReferenceRepository()->getReference(TeamFixtures::TEAM_1_REFERENCE, Team::class);

        $team = $this->teamRepository->findById($team->getId());
        $this->assertNotNull($team);
        $this->assertEquals($team->getId()->getValue(),$team->getId()->getValue());
    }

    public function testDeleteTeam(): void
    {
        $executor = $this->databaseTool->loadFixtures([TeamFixtures::class]);
        $team = $executor->getReferenceRepository()->getReference(TeamFixtures::TEAM_1_REFERENCE, Team::class);

        $team = $this->teamRepository->findById($team->getId());
        $this->assertNotNull($team);

        $this->teamRepository->delete($team);

        $deletedTeam = $this->teamRepository->findById($team->getId());
        $this->assertNull($deletedTeam);
    }

    public function testRemovePlayer(): void
    {
        $executor = $this->databaseTool->loadFixtures([PlayerFixtures::class]);
        $player = $executor->getReferenceRepository()->getReference(PlayerFixtures::PLAYER_1_REFERENCE, Player::class);
        $this->assertNotNull($player);

        $teamId = $player->getTeam()->getId();

        $this->teamRepository->removePlayer($player);

        $team = $this->teamRepository->findById($teamId);
        $this->assertNotNull($team);
        $this->assertNull($team->findPlayerById($player->getId()));
    }
}
