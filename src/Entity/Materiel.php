<?php

namespace App\Entity;

use App\Repository\MaterielRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterielRepository::class)]
class Materiel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $denomination = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateExpiration = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateControle = null;

    #[ORM\Column(nullable: true)]
    private ?int $utilisationMax = null;

    #[ORM\Column]
    private ?bool $estStocke = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $stock = null;

    #[ORM\Column]
    private ?bool $estActive = null;

    #[ORM\Column]
    private ?int $utilisation = null;

    #[ORM\Column(length: 50)]
    private ?string $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'materiels')]
    private ?vehicule $id_vehicule = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function setDenomination(string $denomination): self
    {
        $this->denomination = $denomination;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(?\DateTimeInterface $dateExpiration): self
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
    }

    public function getDateControle(): ?\DateTimeInterface
    {
        return $this->dateControle;
    }

    public function setDateControle(?\DateTimeInterface $dateControle): self
    {
        $this->dateControle = $dateControle;

        return $this;
    }

    public function getUtilisationMax(): ?int
    {
        return $this->utilisationMax;
    }

    public function setUtilisationMax(?int $utilisationMax): self
    {
        $this->utilisationMax = $utilisationMax;

        return $this;
    }

    public function isEstStocke(): ?bool
    {
        return $this->estStocke;
    }

    public function setEstStocke(bool $estStocke): self
    {
        $this->estStocke = $estStocke;

        return $this;
    }

    public function getStock(): ?string
    {
        return $this->stock;
    }

    public function setStock(?string $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function isEstActive(): ?bool
    {
        return $this->estActive;
    }

    public function setEstActive(bool $estActive): self
    {
        $this->estActive = $estActive;

        return $this;
    }

    public function getUtilisation(): ?int
    {
        return $this->utilisation;
    }

    public function setUtilisation(int $utilisation): self
    {
        $this->utilisation = $utilisation;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getIdVehicule(): ?vehicule
    {
        return $this->id_vehicule;
    }

    public function setIdVehicule(?vehicule $id_vehicule): self
    {
        $this->id_vehicule = $id_vehicule;

        return $this;
    }
}
