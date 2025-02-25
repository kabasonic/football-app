<?php

namespace App\Domain\Models\Team\Entity;

use App\Domain\Event\TeamRelocatedEvent;
use App\Domain\Exception\InvalidLocationChangeException;
use App\Domain\Exception\InvalidUlidException;
use App\Domain\Exception\PlayerNotFoundException;
use App\Domain\Exception\TeamPlayerLimitExceededException;
use App\Domain\Models\Team\ValueObject\PlayerId;
use App\Domain\Models\Team\ValueObject\TeamId;
use App\Shared\Domain\Aggregate\AggregateRoot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Team extends AggregateRoot
{
    const MAX_PLAYERS_COUNT = 11;

    private string $id;
    private string $name;
    private string $city;
    private int $yearFounded;
    private string $stadiumName;
    private Collection $players;

    public function __construct(TeamId $id)
    {
        $this->id = $id->getValue();
        $this->players = new ArrayCollection();
    }

    /**
     * @throws InvalidUlidException
     */
    public function getId(): ?TeamId
    {
        return new TeamId($this->id);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getYearFounded(): int
    {
        return $this->yearFounded;
    }

    public function getStadiumName(): string
    {
        return $this->stadiumName;
    }

    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function setYearFounded(int $yearFounded): void
    {
        $this->yearFounded = $yearFounded;
    }

    public function setStadiumName(string $stadiumName): void
    {
        $this->stadiumName = $stadiumName;
    }

    public function findPlayerById(PlayerId $playerId): ?Player
    {
        foreach ($this->players as $player) {
            if ($playerId->getValue() === $player->getId()->getValue()) {
                return $player;
            }
        }
        return null;
    }

    /**
     * @throws TeamPlayerLimitExceededException
     */
    public function addPlayer(Player $player): void
    {
        if ($this->players->count() >= static::MAX_PLAYERS_COUNT) {
            throw new TeamPlayerLimitExceededException(static::MAX_PLAYERS_COUNT);
        }

        $this->players[] = $player;
        $player->setTeam($this);
    }

    /**
     * @throws PlayerNotFoundException
     */
    public function updatePlayer(PlayerId $playerId, string $firstName, string $lastName, int $age, string $position): void
    {
        $player = $this->findPlayerById($playerId);

        if (!$player) {
            throw new PlayerNotFoundException($playerId->getValue());
        }

        $player->update($firstName, $lastName, $age, $position);
    }

    /**
     * @throws PlayerNotFoundException
     */
    public function removePlayer(Player $player): void
    {
        if ($this->players->contains($player)) {
            $this->players->removeElement($player);
            $player->setTeam(null);
        }else{
            throw new PlayerNotFoundException($player->getId()->getValue());
        }
    }

    /**
     * @throws InvalidUlidException
     * @throws InvalidLocationChangeException
     */
    public function relocate(string $newCity): void
    {
        if ($newCity === $this->city) {
            throw new InvalidLocationChangeException($this->city, $newCity);
        }

        $this->city = $newCity;

        $this->recordDomainEvent(new TeamRelocatedEvent($this->getId(), $newCity));
    }

    public function update(string $name, string $city, int $yearFounded, string $stadiumName): void
    {
        $this->name = $name;
        $this->city = $city;
        $this->yearFounded = $yearFounded;
        $this->stadiumName = $stadiumName;
    }

    public static function create(TeamId $id, string $name, string $city, int $yearFounded, string $stadiumName): Team
    {
        $team = new self($id);
        $team->setName($name);
        $team->setCity($city);
        $team->setYearFounded($yearFounded);
        $team->setStadiumName($stadiumName);

        return $team;
    }

}
