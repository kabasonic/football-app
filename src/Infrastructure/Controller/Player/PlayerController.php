<?php

namespace App\Infrastructure\Controller\Player;

use App\Application\Command\AddPlayerToTeamCommand;
use App\Application\Command\RemovePlayerFromTeamCommand;
use App\Application\Command\UpdatePlayerInTeamCommand;
use App\Application\Dto\Request\Player\PlayerPayloadRequest as PlayerPayloadRequestDto;
use App\Application\Query\GetTeamPlayerQuery;
use App\Application\Query\GetTeamPlayersQuery;
use App\Infrastructure\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Throwable;

class PlayerController extends AbstractController
{

    public function list(string $teamId): JsonResponse
    {
        try{
            $players = $this->queryBus->execute(new GetTeamPlayersQuery(teamId: $teamId));

            return $this->serializeResponse(responseData: $players);
        }catch (Throwable $exception){

            return $this->handleExceptionResponse($exception);
        }
    }

    public function details(string $teamId, string $playerId): JsonResponse
    {
        try{
            $query = new GetTeamPlayerQuery(teamId: $teamId, playerId: $playerId);
            $player = $this->queryBus->execute($query);

            return $this->serializeResponse(responseData: $player);
        }catch (Throwable $exception){

            return $this->handleExceptionResponse($exception);
        }
    }

    public function add(
        #[MapRequestPayload] PlayerPayloadRequestDto $requestDto,
        string $teamId
    ): JsonResponse
    {
        try{
            $command = new AddPlayerToTeamCommand(
                teamId: $teamId,
                firstName: $requestDto->firstName,
                lastName: $requestDto->lastName,
                age: $requestDto->age,
                position: $requestDto->position,
            );

            $player = $this->commandBus->execute($command);

            return $this->serializeResponse(
                responseData: $player,
                statusCode: Response::HTTP_CREATED
            );
        }catch (Throwable $exception){

            return $this->handleExceptionResponse($exception);
        }
    }

    public function update(
        #[MapRequestPayload] PlayerPayloadRequestDto $requestDto,
        string $teamId,
        string $playerId
    ): JsonResponse
    {
        try{
            $command = new UpdatePlayerInTeamCommand(
                teamId: $teamId,
                playerId: $playerId,
                firstName: $requestDto->firstName,
                lastName: $requestDto->lastName,
                age: $requestDto->age,
                position: $requestDto->position,
            );

            $player = $this->commandBus->execute($command);

            return $this->serializeResponse(
                responseData: $player
            );
        }catch (Throwable $exception){

            return $this->handleExceptionResponse($exception);
        }
    }

    public function remove(string $teamId, string $playerId): JsonResponse
    {
        try{
            $command = new RemovePlayerFromTeamCommand(
                teamId: $teamId,
                playerId: $playerId
            );

            $this->commandBus->execute($command);

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }catch (Throwable $exception){

            return $this->handleExceptionResponse($exception);
        }
    }
}
