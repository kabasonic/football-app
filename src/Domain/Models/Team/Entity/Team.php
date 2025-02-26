<?php

namespace App\Domain\Models\Team\Entity;

use App\Domain\Event\TeamRelocatedEvent;
use App\Domain\Exception\InvalidLocationChangeException;
use App\Domain\Exception\InvalidPlayerAgeException;
use App\Domain\Exception\InvalidPlayerPositionException;
use App\Domain\Exception\InvalidTeamYearFoundedException;
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
    private const MAX_PLAYERS_COUNT = 11;
    private const MIN_YEAR_FOUNDED = 1800;

    private string $id;
    private string $name;
    private string $city;
    private int $yearFounded;
    private string $stadiumName;
    private Collection $players;

    /**
     * @throws InvalidTeamYearFoundedException
     */
    public function __construct(TeamId $id, string $name, string $city, int $yearFounded, string $stadiumName)
    {
        $this->validateYearFounded($yearFounded);

        $this->id = $id->getValue();
        $this->name = $name;
        $this->city = $city;
        $this->yearFounded = $yearFounded;
        $this->stadiumName = $stadiumName;
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
        if ($this->players->count() >= Team::MAX_PLAYERS_COUNT) {
            throw new TeamPlayerLimitExceededException(Team::MAX_PLAYERS_COUNT);
        }

        $this->players[] = $player;
        $player->setTeam($this);
    }

    /**
     * @param PlayerId $playerId
     * @param string $firstName
     * @param string $lastName
     * @param int $age
     * @param string $position
     * @throws PlayerNotFoundException
     * @throws InvalidPlayerAgeException
     * @throws InvalidPlayerPositionException
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
     * @throws InvalidTeamYearFoundedException
     */
    private function validateYearFounded(int $yearFounded): void
    {
        $currentYear = (int) date('Y');

        if ($yearFounded < self::MIN_YEAR_FOUNDED || $yearFounded > $currentYear) {
            throw new InvalidTeamYearFoundedException($yearFounded, self::MIN_YEAR_FOUNDED, $currentYear);
        }
    }

    /**
     * @throws PlayerNotFoundException
     * @throws InvalidUlidException
     */
    public function removePlayer(Player $player): void
    {
        if (!$this->players->contains($player)) {
            throw new PlayerNotFoundException($player->getId()->getValue());
        }

        $this->players->removeElement($player);
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

    /**
     * @throws InvalidTeamYearFoundedException
     */
    public function update(string $name, string $city, int $yearFounded, string $stadiumName): void
    {
        $this->validateYearFounded($yearFounded);

        $this->name = $name;
        $this->city = $city;
        $this->yearFounded = $yearFounded;
        $this->stadiumName = $stadiumName;
    }

    /**
     * @throws InvalidTeamYearFoundedException
     */
    public static function create(TeamId $id, string $name, string $city, int $yearFounded, string $stadiumName): Team
    {
        return new self(
            id: $id,
            name: $name,
            city: $city,
            yearFounded: $yearFounded,
            stadiumName: $stadiumName
        );
    }

}
