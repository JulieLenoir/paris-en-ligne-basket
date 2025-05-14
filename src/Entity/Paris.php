<?php

namespace App\Entity;

use App\Repository\ParisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParisRepository::class)]
class Paris
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Statut = null;

    #[ORM\Column]
    private ?int $Gains = null;

    #[ORM\Column]
    private ?int $Perte = null;

    #[ORM\Column]
    private ?int $Mise = null;

    #[ORM\ManyToOne(inversedBy: 'paris')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'paris')]
    private ?Matchs $match = null;

    #[ORM\Column(length: 255)]
    private ?string $equipe = null;

    #[ORM\Column(nullable: true)]
    private ?int $soldeCloture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?string
    {
        return $this->Statut;
    }

    public function setStatut(string $Statut): static
    {
        $this->Statut = $Statut;

        return $this;
    }

    public function getGains(): ?int
    {
        return $this->Gains;
    }

    public function setGains(int $Gains): static
    {
        $this->Gains = $Gains;

        return $this;
    }

    public function getPerte(): ?int
    {
        return $this->Perte;
    }

    public function setPerte(int $Perte): static
    {
        $this->Perte = $Perte;

        return $this;
    }

    public function getMise(): ?int
    {
        return $this->Mise;
    }

    public function setMise(int $Mise): static
    {
        $this->Mise = $Mise;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMatch(): ?Matchs
    {
        return $this->match;
    }

    public function setMatch(?Matchs $match): static
    {
        $this->match = $match;

        return $this;
    }

        // Ajoute un attribut `matchId` pour stocker l'ID du match récupéré
    private ?string $matchId = null;

    // Getter pour `matchId`
    public function getMatchId(): ?string
    {
        return $this->matchId;
    }

    // Setter pour `matchId`
    public function setMatchId(?string $matchId): self
    {
        $this->matchId = $matchId;
        return $this;
    }


    public function getEquipe(): ?string
    {
        return $this->equipe;
    }

    public function setEquipe(string $equipe): static
    {
        $this->equipe = $equipe;
        return $this;
    }



    public function getSoldeCloture(): ?int
    {
        return $this->soldeCloture;
    }

    public function setSoldeCloture(?int $soldeCloture): static
    {
        $this->soldeCloture = $soldeCloture;
        return $this;
    }

}
