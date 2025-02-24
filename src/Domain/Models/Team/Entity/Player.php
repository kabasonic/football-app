<?php

namespace App\Domain\Models\Team\Entity;

use App\Domain\Models\Team\ValueObject\PlayerId;

class Player
{
    private string $id;
    private string $firstName;
    private string $lastName;
    private int $age;
    private string $position;
    private Team $team;

    public function __construct(PlayerId $id, string $firstName, string $lastName, int $age, string $position)
    {
        $this->id = $id->getValue();
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->age = $age;
        $this->position = $position;
    }

    public function getId(): ?PlayerId
    {
        return new PlayerId($this->id);
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): void
    {
        $this->team = $team;
    }

}

