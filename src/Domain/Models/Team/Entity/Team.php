<?php

namespace App\Domain\Models\Team\Entity;

use App\Domain\Event\TeamRelocatedEvent;
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

    public function getPlayers(): ArrayCollection
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

    public function update(string $name, string $city, int $yearFounded, string $stadiumName): void
    {
        $this->name = $name;
        $this->city = $city;
        $this->yearFounded = $yearFounded;
        $this->stadiumName = $stadiumName;
    }

    public function addPlayer(Player $player): void
    {
        if ($this->players->count() >= static::MAX_PLAYERS_COUNT) {
            throw new \DomainException("A team cannot have more than " . static::MAX_PLAYERS_COUNT . " players.");
        }

        $this->players[] = $player;
        $player->setTeam($this);
    }

    public function removePlayer(Player $player): void
    {
        if ($this->players->contains($player)) {
            $this->players->removeElement($player);
            $player->setTeam(null);
        }
    }

    public function relocate(string $newCity): void
    {
        if ($newCity === $this->city) {
            throw new \DomainException('New location must be different from the current one.');
        }

        $this->city = $newCity;

        $this->recordDomainEvent(new TeamRelocatedEvent($this->getId(), $newCity));
    }

    public static function create(TeamId $id, string $name, string $city, int $yearFounded, string $stadiumName): Team
    {
        $article = new self($id);
        $article->setName($name);
        $article->setCity($city);
        $article->setYearFounded($yearFounded);
        $article->setStadiumName($stadiumName);

        return $article;
    }

}
