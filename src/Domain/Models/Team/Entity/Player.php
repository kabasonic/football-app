<?php

namespace App\Domain\Models\Team\Entity;

use App\Domain\Exception\InvalidPlayerAgeException;
use App\Domain\Exception\InvalidPlayerPositionException;
use App\Domain\Exception\InvalidUlidException;
use App\Domain\Models\Team\ValueObject\PlayerId;

class Player
{
    private const VALID_POSITIONS = [
        'GOALKEEPER',
        'DEFENDER',
        'MIDFIELDER',
        'FORWARD',
    ];

    private const MINIMUM_AGE = 12;
    private const MAXIMUM_AGE = 50;

    private string $id;
    private string $firstName;
    private string $lastName;
    private int $age;
    private string $position;
    private Team $team;

    /**
     * @throws InvalidPlayerPositionException
     * @throws InvalidPlayerAgeException
     */
    public function __construct(PlayerId $id, string $firstName, string $lastName, int $age, string $position)
    {
        $this->validateAge($age);
        $this->validatePosition($position);

        $this->id = $id->getValue();
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->age = $age;
        $this->position = strtoupper($position);
    }

    /**
     * @throws InvalidPlayerAgeException
     */
    private function validateAge(int $age): void
    {
        if($age < self::MINIMUM_AGE || $age > self::MAXIMUM_AGE) {
            throw new InvalidPlayerAgeException($age, self::MINIMUM_AGE, self::MAXIMUM_AGE);
        }
    }

    /**
     * @throws InvalidPlayerPositionException
     */
    private function validatePosition(string $position): void
    {
        if(!in_array(strtoupper($position), self::VALID_POSITIONS)) {
            throw new InvalidPlayerPositionException($position, self::VALID_POSITIONS);
        }
    }

    /**
     * @throws InvalidUlidException
     */
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

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): void
    {
        $this->team = $team;
    }

    /**
     * @throws InvalidPlayerPositionException
     * @throws InvalidPlayerAgeException
     */
    public function update(string $firstName, string $lastName, int $age, string $position): void
    {
        $this->validateAge($age);
        $this->validatePosition($position);

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->age = $age;
        $this->position = strtoupper($position);
    }

}

