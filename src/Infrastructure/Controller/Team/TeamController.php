<?php

namespace App\Infrastructure\Controller\Team;

use App\Application\Command\CreateTeamCommand;
use App\Application\Command\DeleteTeamCommand;
use App\Application\Command\RelocateTeamCommand;
use App\Application\Command\UpdateTeamCommand;
use App\Application\Dto\Request\Team\CreateTeamRequest as CreateTeamRequestDto;
use App\Application\Dto\Request\Team\RelocateTeamRequest as RelocateTeamRequestDto;
use App\Application\Dto\Request\Team\UpdateTeamRequest as UpdateTeamRequestDto;
use App\Application\Query\GetTeamQuery;
use App\Application\Query\GetTeamsQuery;
use App\Infrastructure\Controller\AbstractController;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Throwable;

class TeamController extends AbstractController
{
    public function list(): JsonResponse
    {
        try {
            $teams = $this->queryBus->execute(new GetTeamsQuery());

            return $this->serializeResponse(responseData: $teams);
        }catch (Throwable $exception){

            return $this->handleExceptionResponse($exception);
        }
    }

    public function details(string $teamId): JsonResponse
    {
        try {
            $team = $this->queryBus->execute(new GetTeamQuery($teamId));

            return $this->serializeResponse(responseData: $team);
        }catch (Throwable $exception){

            return $this->handleExceptionResponse($exception);
        }
    }

    public function create(#[MapRequestPayload] CreateTeamRequestDto $requestDto): JsonResponse
    {
        try {
            $command = new CreateTeamCommand(
                name: $requestDto->name,
                city: $requestDto->city,
                yearFounded: $requestDto->yearFounded,
                stadiumName: $requestDto->stadiumName,
            );
            $team = $this->commandBus->execute($command);

            return $this->serializeResponse(
                responseData: $team,
                statusCode: Response::HTTP_CREATED
            );
        }catch (Throwable $exception){

            return $this->handleExceptionResponse($exception);
        }
    }

    public function update(
        #[MapRequestPayload] UpdateTeamRequestDto $requestDto,
        string                                    $teamId
    ): JsonResponse
    {
        try {
            $command = new UpdateTeamCommand(
                id: $teamId,
                name: $requestDto->name,
                city: $requestDto->city,
                yearFounded: $requestDto->yearFounded,
                stadiumName: $requestDto->stadiumName,
            );
            $team = $this->commandBus->execute($command);

            return $this->serializeResponse(
                responseData: $team,
            );
        }catch (Throwable $exception){

            return $this->handleExceptionResponse($exception);
        }
    }

    public function delete(string $teamId): JsonResponse
    {
        try {
            $this->commandBus->execute(new DeleteTeamCommand($teamId));

            return $this->serializeResponse(
                responseData: null,
                statusCode: Response::HTTP_NO_CONTENT
            );
        }catch (Throwable $exception){

            return $this->handleExceptionResponse($exception);
        }
    }

    public function relocate(
        #[MapRequestPayload] RelocateTeamRequestDto $requestDto,
        string                                      $teamId
    ): JsonResponse
    {
        try {
            $command = new RelocateTeamCommand(
                teamId: $teamId,
                city: $requestDto->city
            );

            $team = $this->commandBus->execute($command);

            return $this->serializeResponse(responseData: $team);
        }catch (Throwable $exception){

            return $this->handleExceptionResponse($exception);
        }
    }
}
