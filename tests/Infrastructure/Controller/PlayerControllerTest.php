<?php

namespace App\Tests\Infrastructure\Controller;

use App\Domain\Models\Team\Entity\Player;
use App\Domain\Models\Team\Entity\Team;
use App\Shared\Domain\Services\UlidService;
use App\Tests\Resource\Fixtures\PlayerFixtures;
use App\Tests\Resource\Fixtures\TeamFixtures;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PlayerControllerTest extends WebTestCase
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

    public function testListTeamPlayers(): void
    {
        $executor = $this->databaseTool->loadFixtures([TeamFixtures::class]);
        $team = $executor->getReferenceRepository()->getReference(TeamFixtures::TEAM_1_REFERENCE, Team::class);

        $this->client->request('GET', "/api/teams/{$team->getId()->getValue()}/players");
        $this->assertResponseStatusCodeSame(200);

        $responseData = $this->getJsonResponse();
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
    }

    public function testDetailsPlayer(): void
    {
        $executor = $this->databaseTool->loadFixtures([TeamFixtures::class, PLayerFixtures::class]);
        $player = $executor->getReferenceRepository()->getReference(PlayerFixtures::PLAYER_1_REFERENCE, Player::class);

        $this->client->request('GET', "/api/teams/{$player->getTeam()->getId()->getValue()}/players/{$player->getId()->getValue()}");
        $this->assertResponseStatusCodeSame(200);

        $responseData = $this->getJsonResponse();
        $this->assertValidPlayerStructure($responseData['data']);
    }

    public function testCreatePlayer(): void
    {
        $executor = $this->databaseTool->loadFixtures([TeamFixtures::class]);
        $team = $executor->getReferenceRepository()->getReference(TeamFixtures::TEAM_1_REFERENCE, Team::class);

        $data = [
            'firstName' => 'Jan',
            'lastName' => 'Pavlo',
            'age' => 23,
            'position' => 'DEFENDER',
        ];

        $this->client->request(
            'POST',
            "/api/teams/{$team->getId()->getValue()}/players",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $this->assertResponseStatusCodeSame(201);

        $responseData = $this->getJsonResponse();
        $this->assertValidPlayerStructure($responseData['data']);
    }

    public function testMaxPlayersOnCreate(): void
    {
        $executor = $this->databaseTool->loadFixtures([TeamFixtures::class, PlayerFixtures::class]);
        $team = $executor->getReferenceRepository()->getReference(TeamFixtures::TEAM_1_REFERENCE, Team::class);

        $data = [
            'firstName' => 'Jan',
            'lastName' => 'Pavlo',
            'age' => 23,
            'position' => 'FORWARD',
        ];

        $this->client->request('POST', "/api/teams/{$team->getId()->getValue()}/players", [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
        $this->assertResponseStatusCodeSame(409);
    }

    public function testUpdatePlayer(): void
    {
        $executor = $this->databaseTool->loadFixtures([TeamFixtures::class, PlayerFixtures::class]);
        $player = $executor->getReferenceRepository()->getReference(PlayerFixtures::PLAYER_1_REFERENCE, Player::class);

        $data = ['firstName' => 'Updated', 'lastName' => 'Player', 'age' => 25, 'position' => 'MIDFIELDER'];

        $this->client->request('PUT', "/api/teams/{$player->getTeam()->getId()->getValue()}/players/{$player->getId()->getValue()}", [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
        $this->assertResponseStatusCodeSame(200);

        $responseData = $this->getJsonResponse();
        $this->assertValidPlayerStructure($responseData['data']);
    }

    public function testDeletePlayer(): void
    {
        $executor = $this->databaseTool->loadFixtures([TeamFixtures::class, PlayerFixtures::class]);
        $player = $executor->getReferenceRepository()->getReference(PlayerFixtures::PLAYER_1_REFERENCE, Player::class);

        $this->client->request('DELETE', "/api/teams/{$player->getTeam()->getId()->getValue()}/players/{$player->getId()->getValue()}");
        $this->assertResponseStatusCodeSame(204);
    }

    public function testPlayerNotFoundError(): void
    {
        $teamId = UlidService::generate();
        $playerId = UlidService::generate();

        $this->client->request('GET', "/api/teams/{$teamId}/players/{$playerId}");
        $this->assertResponseStatusCodeSame(404);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }

    private function getJsonResponse(): array
    {
        $content = $this->client->getResponse()->getContent();
        $this->assertJson($content);
        return json_decode($content, true);
    }

    private function assertValidPlayerStructure(array $player): void
    {
        $expectedKeys = ['id', 'firstName', 'lastName', 'age', 'position', 'teamId'];
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $player);
        }

        $this->assertIsString($player['id']);
        $this->assertIsString($player['firstName']);
        $this->assertIsString($player['lastName']);
        $this->assertIsInt($player['age']);
        $this->assertIsString($player['position']);
        $this->assertIsString($player['teamId']);
    }
}
