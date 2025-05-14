<?php

namespace App\Entity;

use App\Repository\MatchsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchsRepository::class)]
class Matchs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $homeTeamName = null;

    #[ORM\Column(length: 255)]
    private ?string $awayTeamName = null;

    #[ORM\Column(length: 255)]
    private ?string $scheduled_time = null;

    #[ORM\Column]
    private ?int $home_points = null;

    #[ORM\Column]
    private ?int $away_points = null;

    /**
     * @var Collection<int, Paris>
     */
    #[ORM\OneToMany(targetEntity: Paris::class, mappedBy: 'match')]
    private Collection $paris;

    #[ORM\Column]
    private ?string $Game_id = null;

    public function __construct()
    {
        $this->paris = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHomeTeamName(): ?string
    {
        return $this->homeTeamName;
    }

    public function setHomeTeamName(string $homeTeamName): static
    {
        $this->homeTeamName = $homeTeamName;

        return $this;
    }

    public function getAwayTeamName(): ?string
    {
        return $this->awayTeamName;
    }

    public function setAwayTeamName(string $awayTeamName): static
    {
        $this->awayTeamName = $awayTeamName;

        return $this;
    }

    public function getScheduledTime(): ?string
    {
        return $this->scheduled_time;
    }

    public function setScheduledTime(string $scheduled_time): static
    {
        $this->scheduled_time = $scheduled_time;

        return $this;
    }

    public function getHomePoints(): ?int
    {
        return $this->home_points;
    }

    public function setHomePoints(int $home_points): static
    {
        $this->home_points = $home_points;

        return $this;
    }

    public function getAwayPoints(): ?int
    {
        return $this->away_points;
    }

    public function setAwayPoints(int $away_points): static
    {
        $this->away_points = $away_points;

        return $this;
    }

    /**
     * @return Collection<int, Paris>
     */
    public function getParis(): Collection
    {
        return $this->paris;
    }

    public function addPari(Paris $pari): static
    {
        if (!$this->paris->contains($pari)) {
            $this->paris->add($pari);
            $pari->setMatch($this);
        }

        return $this;
    }

    public function removePari(Paris $pari): static
    {
        if ($this->paris->removeElement($pari)) {
            // set the owning side to null (unless already changed)
            if ($pari->getMatch() === $this) {
                $pari->setMatch(null);
            }
        }

        return $this;
    }

    public function getGameId(): ?string
    {
        return $this->Game_id;
    }

    public function setGameId(string $Game_id): static
    {
        $this->Game_id = $Game_id;

        return $this;
    }
}