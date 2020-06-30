<?php

namespace App\Entity;

use App\Repository\MatieresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MatieresRepository::class)
 */
class Matieres
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom_matiere;

    /**
     * @ORM\ManyToMany(targetEntity=Profs::class, mappedBy="id_matiere")
     */
    private $profs;

    /**
     * @ORM\OneToMany(targetEntity=Cours::class, mappedBy="id_matiere")
     */
    private $cours;

    public function __construct()
    {
        $this->profs = new ArrayCollection();
        $this->cours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomMatiere(): ?string
    {
        return $this->nom_matiere;
    }

    public function setNomMatiere(string $nom_matiere): self
    {
        $this->nom_matiere = $nom_matiere;

        return $this;
    }

    /**
     * @return Collection|profs[]
     */
    public function getProfs(): Collection
    {
        return $this->profs;
    }

    public function addProf(profs $prof): self
    {
        if (!$this->profs->contains($prof)) {
            $this->profs[] = $prof;
            $prof->addIdMatiere($this);
        }

        return $this;
    }

    public function removeProf(profs $prof): self
    {
        if ($this->profs->contains($prof)) {
            $this->profs->removeElement($prof);
            $prof->removeIdMatiere($this);
        }

        return $this;
    }

    /**
     * @return Collection|Cours[]
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours[] = $cour;
            $cour->setIdMatiere($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->contains($cour)) {
            $this->cours->removeElement($cour);
            // set the owning side to null (unless already changed)
            if ($cour->getIdMatiere() === $this) {
                $cour->setIdMatiere(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNomMatiere();
    }
}
