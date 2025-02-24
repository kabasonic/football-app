<?php

namespace App\Tests\Domain\Repository;

use App\Domain\Models\Team\Entity\Team;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Tests\Resource\Fixtures\TeamFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TeamRepositoryTest extends WebTestCase
{
    private TeamRepositoryInterface $teamRepository;
    private DatabaseToolCollection $databaseToolCollection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = static::getContainer()->get(TeamRepositoryInterface::class);
        $this->databaseToolCollection = static::getContainer()->get(DatabaseToolCollection::class);
    }

    public function testCreateTeam(): void
    {
        $databaseTool = $this->databaseToolCollection->get();
        $executor = $databaseTool->loadFixtures([TeamFixtures::class]);

        $team = $executor->getReferenceRepository()->getReference(TeamFixtures::TEAM_1_REFERENCE, Team::class);

        $existingTeam = $this->teamRepository->findById($team->getId());

        $this->assertEquals($team->getId(), $existingTeam->getId());
    }

    public function testDeleteTeam(): void
    {
        $databaseTool = $this->databaseToolCollection->get();
        $executor = $databaseTool->loadFixtures([TeamFixtures::class]);

        $team = $executor->getReferenceRepository()->getReference(TeamFixtures::TEAM_1_REFERENCE, Team::class);

        $this->teamRepository->delete($team);

        $this->getContainer()->get('doctrine')->getManager()->flush();

        $deletedTeam = $this->teamRepository->findById($team->getId());
        $this->assertNull($deletedTeam);
    }

    public function testFindByIdTeam(): void
    {
        $databaseTool = $this->databaseToolCollection->get();
        $executor = $databaseTool->loadFixtures([TeamFixtures::class]);

        $team = $executor->getReferenceRepository()->getReference(TeamFixtures::TEAM_1_REFERENCE, Team::class);

        $existingTeam = $this->teamRepository->findById($team->getId());

        $this->assertEquals($team->getId(), $existingTeam->getId());
    }
}
