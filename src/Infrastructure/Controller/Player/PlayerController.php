<?php

namespace App\Infrastructure\Controller\Player;

use App\Application\Command\Team\AddPlayerToTeamCommand;
use App\Application\Command\Team\RemovePlayerFromTeamCommand;
use App\Infrastructure\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PlayerController extends AbstractController
{

    public function add(string $teamId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $command = new AddPlayerToTeamCommand(
            teamId: $teamId,
            firstName: $data['firstName'],
            lastName: $data['lastName'],
            age: $data['age'],
            position: $data['position']
        );

        $playerId = $this->commandBus->execute($command);
        return new JsonResponse(['id' => $playerId], Response::HTTP_CREATED);
    }

    public function remove(string $teamId, string $playerId): JsonResponse
    {
        $command = new RemovePlayerFromTeamCommand($teamId, $playerId);
        $this->commandBus->execute($command);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
