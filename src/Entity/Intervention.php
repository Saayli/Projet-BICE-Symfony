<?php

namespace App\Entity;

use App\Repository\InterventionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionRepository::class)]
class Intervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 50)]
    private ?string $denomination = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Vehicule::class, inversedBy: 'id_intervention')]
    private Collection $id_vehicule;

    public function __construct()
    {
        $this->id_vehicule = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, vehicule>
     */
    public function getIdVehicule(): Collection
    {
        return $this->id_vehicule;
    }

    public function addIdVehicule(vehicule $idVehicule): self
    {
        if (!$this->id_vehicule->contains($idVehicule)) {
            $this->id_vehicule->add($idVehicule);
        }

        return $this;
    }

    public function removeIdVehicule(vehicule $idVehicule): self
    {
        $this->id_vehicule->removeElement($idVehicule);

        return $this;
    }
}
