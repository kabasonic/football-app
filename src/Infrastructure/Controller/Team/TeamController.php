<?php

namespace App\Infrastructure\Controller\Team;

use App\Application\Command\Team\CreateTeamCommand;
use App\Application\Command\Team\DeleteTeamCommand;
use App\Application\Command\Team\RelocateTeamCommand;
use App\Application\Command\Team\UpdateTeamCommand;
use App\Application\Query\Team\GetTeamQuery;
use App\Application\Query\Team\GetTeamsQuery;
use App\Infrastructure\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TeamController extends AbstractController
{
    public function list(): JsonResponse
    {
        $teams = $this->queryBus->execute(new GetTeamsQuery());

        $jsonTeams = $this->serializer->serialize($teams, 'json');

        return new JsonResponse($jsonTeams, Response::HTTP_OK, [], true);
    }

    public function details(string $teamId): JsonResponse
    {
        $team = $this->queryBus->execute(new GetTeamQuery($teamId));

        $jsonTeam = $this->serializer->serialize($team, 'json');

        return new JsonResponse($jsonTeam, Response::HTTP_OK, [], true);
    }

    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $command = new CreateTeamCommand(
            name: $data['name'],
            city: $data['city'],
            yearFounded: $data['yearFounded'],
            stadiumName: $data['stadiumName']
        );

        $teamId = $this->commandBus->execute($command);
        return new JsonResponse(['id' => $teamId], Response::HTTP_CREATED);
    }

    public function update(string $teamId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $command = new UpdateTeamCommand(
            id: $teamId,
            name: $data['name'],
            city: $data['city'],
            yearFounded: $data['yearFounded'],
            stadiumName: $data['stadiumName']
        );

        $this->commandBus->execute($command);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function delete(string $teamId): JsonResponse
    {
        $this->commandBus->execute(new DeleteTeamCommand($teamId));
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function relocate(string $teamId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $command = new RelocateTeamCommand($teamId, $data['newCity']);
        $this->commandBus->execute($command);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
