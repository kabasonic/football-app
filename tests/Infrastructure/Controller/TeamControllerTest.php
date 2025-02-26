<?php

namespace App\Tests\Infrastructure\Controller;

use App\Domain\Models\Team\Entity\Team;
use App\Shared\Domain\Services\UlidService;
use App\Tests\Resource\Fixtures\TeamFixtures;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TeamControllerTest extends WebTestCase
{
    private $client;
    private AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        StaticDriver::setKeepStaticConnections(false);
    }

    public function testListTeams(): void
    {
        $this->client->request('GET', '/api/teams');

        $this->assertResponseStatusCodeSame(200);
        $responseData = $this->getJsonResponse();

        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        $this->assertNotEmpty($responseData['data']);

        foreach ($responseData['data'] as $team) {
            $this->assertValidTeamStructure($team);
        }
    }

    public function testDetailsTeam(): void
    {
        $executor = $this->databaseTool->loadFixtures([TeamFixtures::class]);
        $team = $executor->getReferenceRepository()->getReference(TeamFixtures::TEAM_1_REFERENCE, Team::class);

        $this->client->request('GET', "/api/teams/{$team->getId()->getValue()}");

        $this->assertResponseStatusCodeSame(200);
        $responseData = $this->getJsonResponse();

        $this->assertArrayHasKey('data', $responseData);
        $this->assertValidTeamStructure($responseData['data']);
    }

    public function testCreateTeam(): void
    {
        $data = [
            'name' => 'New Team',
            'city' => 'New City',
            'yearFounded' => 2020,
            'stadiumName' => 'New Stadium',
        ];

        $this->client->request('POST', '/api/teams', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertResponseStatusCodeSame(201);
        $responseData = $this->getJsonResponse();

        $this->assertArrayHasKey('data', $responseData);
        $team = $responseData['data'];

        $this->assertValidTeamStructure($team);

        $this->assertEquals('New Team', $team['name']);
        $this->assertEquals('New City', $team['city']);
        $this->assertEquals(2020, $team['yearFounded']);
        $this->assertEquals('New Stadium', $team['stadiumName']);
    }

    public function testUpdateTeam(): void
    {
        $executor = $this->databaseTool->loadFixtures([TeamFixtures::class]);
        $team = $executor->getReferenceRepository()->getReference(TeamFixtures::TEAM_1_REFERENCE, Team::class);

        $data = [
            'name' => 'Updated Team',
            'city' => 'Updated City',
            'yearFounded' => 2022,
            'stadiumName' => 'Updated Stadium',
        ];

        $this->client->request('PUT', "/api/teams/{$team->getId()->getValue()}", [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertResponseStatusCodeSame(200);
        $responseData = $this->getJsonResponse();

        $this->assertArrayHasKey('data', $responseData);
        $this->assertValidTeamStructure($responseData['data']);
    }

    public function testDeleteTeam(): void
    {
        $executor = $this->databaseTool->loadFixtures([TeamFixtures::class]);
        $team = $executor->getReferenceRepository()->getReference(TeamFixtures::TEAM_3_REFERENCE, Team::class);

        $this->client->request('DELETE', "/api/teams/{$team->getId()->getValue()}");

        $this->assertResponseStatusCodeSame(204);
    }

    public function testRelocateTeam(): void
    {
        $executor = $this->databaseTool->loadFixtures([TeamFixtures::class]);
        $team = $executor->getReferenceRepository()->getReference(TeamFixtures::TEAM_1_REFERENCE, Team::class);

        $data = ['city' => 'New Relocated City'];

        $this->client->request('PUT', "/api/teams/{$team->getId()->getValue()}/relocate", [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertResponseStatusCodeSame(200);
        $responseData = $this->getJsonResponse();

        $this->assertArrayHasKey('data', $responseData);
        $this->assertValidTeamStructure($responseData['data']);
    }

    public function testTeamNotFoundError(): void
    {
        $teamId = UlidService::generate();
        $this->client->request('GET', "/api/teams/$teamId");

        $this->assertResponseStatusCodeSame(404);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }

    /**
     * Helper method to parse JSON response.
     */
    private function getJsonResponse(): array
    {
        $content = $this->client->getResponse()->getContent();
        $this->assertJson($content);

        return json_decode($content, true);
    }

    /**
     * Helper method to validate team structure.
     */
    private function assertValidTeamStructure(array $team): void
    {
        $expectedKeys = ['id', 'name', 'city', 'yearFounded', 'stadiumName', 'players'];
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $team);
        }

        $this->assertIsString($team['id']);
        $this->assertIsString($team['name']);
        $this->assertIsString($team['city']);
        $this->assertIsInt($team['yearFounded']);
        $this->assertIsString($team['stadiumName']);
        $this->assertIsArray($team['players']);

        if (!empty($team['players'])) {
            foreach ($team['players'] as $player) {
                $this->assertValidPlayerStructure($player);
            }
        }
    }

    /**
     * Helper method to validate player structure.
     */
    private function assertValidPlayerStructure(array $player): void
    {
        $expectedKeys = ['id', 'firstName', 'lastName', 'age', 'position'];
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $player);
        }

        $this->assertIsString($player['id']);
        $this->assertIsString($player['firstName']);
        $this->assertIsString($player['lastName']);
        $this->assertIsInt($player['age']);
        $this->assertIsString($player['position']);
    }
}
