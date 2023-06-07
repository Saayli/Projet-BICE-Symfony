<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $denomination = null;

    #[ORM\Column(length: 50)]
    private ?string $numero = null;

    #[ORM\Column(length: 50)]
    private ?string $immatriculation = null;

    #[ORM\Column]
    private ?bool $estActive = null;

    #[ORM\ManyToMany(targetEntity: Intervention::class, mappedBy: 'id_vehicule')]
    private Collection $id_intervention;

    #[ORM\OneToMany(mappedBy: 'id_vehicule', targetEntity: Materiel::class)]
    private Collection $materiels;

    public function __construct()
    {
        $this->id_intervention = new ArrayCollection();
        $this->materiels = new ArrayCollection();
    }

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

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): self
    {
        $this->immatriculation = $immatriculation;

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

    /**
     * @return Collection<int, Intervention>
     */
    public function getIdIntervention(): Collection
    {
        return $this->id_intervention;
    }

    public function addIdIntervention(Intervention $idIntervention): self
    {
        if (!$this->id_intervention->contains($idIntervention)) {
            $this->id_intervention->add($idIntervention);
            $idIntervention->addIdVehicule($this);
        }

        return $this;
    }

    public function removeIdIntervention(Intervention $idIntervention): self
    {
        if ($this->id_intervention->removeElement($idIntervention)) {
            $idIntervention->removeIdVehicule($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Materiel>
     */
    public function getMateriels(): Collection
    {
        return $this->materiels;
    }

    public function addMateriel(Materiel $materiel): self
    {
        if (!$this->materiels->contains($materiel)) {
            $this->materiels->add($materiel);
            $materiel->setIdVehicule($this);
        }

        return $this;
    }

    public function removeMateriel(Materiel $materiel): self
    {
        if ($this->materiels->removeElement($materiel)) {
            // set the owning side to null (unless already changed)
            if ($materiel->getIdVehicule() === $this) {
                $materiel->setIdVehicule(null);
            }
        }

        return $this;
    }
}
