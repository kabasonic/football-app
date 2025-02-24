<?php

namespace App\Infrastructure\Controller;

use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractController
{
    public function __construct(
        protected CommandBusInterface $commandBus,
        protected QueryBusInterface   $queryBus,
        protected SerializerInterface $serializer
    )
    {
    }
}
