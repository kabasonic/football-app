<?php

namespace App\Infrastructure\Controller;

use App\Domain\Exception\InvalidLocationChangeException;
use App\Domain\Exception\InvalidPlayerAgeException;
use App\Domain\Exception\InvalidPlayerPositionException;
use App\Domain\Exception\InvalidTeamYearFoundedException;
use App\Domain\Exception\InvalidUlidException;
use App\Domain\Exception\PlayerNotFoundException;
use App\Domain\Exception\TeamNotFoundException;
use App\Domain\Exception\TeamPlayerLimitExceededException;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

abstract class AbstractController
{
    public function __construct(
        protected CommandBusInterface $commandBus,
        protected QueryBusInterface   $queryBus,
        protected SerializerInterface $serializer
    )
    {
    }

    protected function serializeResponse(
        mixed  $responseData,
        int    $statusCode = Response::HTTP_OK,
        string $format = 'json'
    ): JsonResponse
    {
        $json = $this->serializer->serialize(['data' => $responseData], $format);

        return JsonResponse::fromJsonString($json, $statusCode);
    }

    protected function handleExceptionResponse(
        Throwable $exception,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR
    ): JsonResponse {

        $errors = [];

        foreach ($exception->getWrappedExceptions() as $exception) {
            $statusCode = match (true) {
                $exception instanceof InvalidLocationChangeException,
                $exception instanceof InvalidPlayerAgeException,
                $exception instanceof InvalidPlayerPositionException,
                $exception instanceof InvalidTeamYearFoundedException,
                $exception instanceof InvalidUlidException => Response::HTTP_BAD_REQUEST,

                $exception instanceof PlayerNotFoundException,
                $exception instanceof TeamNotFoundException => Response::HTTP_NOT_FOUND,

                $exception instanceof TeamPlayerLimitExceededException => Response::HTTP_CONFLICT,
                default => $statusCode,
            };

            $errors[] = [
                'message' => $exception->getMessage(),
            ];
        }

        return new JsonResponse([
            'errors' => $errors,
            'code' => $statusCode,
        ], $statusCode);
    }

}
