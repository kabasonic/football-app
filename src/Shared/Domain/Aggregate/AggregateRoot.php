<?php

namespace App\Shared\Domain\Aggregate;


use App\Shared\Domain\Event\DomainEventInterface;

abstract class AggregateRoot
{
    protected array $domainEvents;

    public function recordDomainEvent(DomainEventInterface $event): self
    {
        $this->domainEvents[] = $event;

        return $this;
    }

    public function pullDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }

    public function hasRecordedDomainEvent(string $eventClass): bool
    {
        foreach ($this->domainEvents as $event) {
            if ($event instanceof $eventClass) {
                return true;
            }
        }

        return false;
    }
}
