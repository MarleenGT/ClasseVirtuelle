<?php

namespace App\Entity;

use App\Repository\ClassesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClassesRepository::class)
 */
class Classes
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
    private $nom_classe;

    /**
     * @ORM\OneToMany(targetEntity=Eleves::class, mappedBy="id_classe")
     */
    private $eleves;

    /**
     * @ORM\OneToMany(targetEntity=Cours::class, mappedBy="id_classe", cascade={"persist", "remove"})
     */
    private $cours;

    /**
     * @return mixed
     */
    public function getArchives()
    {
        return $this->archives;
    }

    /**
     * @ORM\OneToMany(targetEntity=Archives::class, mappedBy="id_classe", cascade={"persist", "remove"})
     */
    private $archives;

    /**
     * @ORM\ManyToMany(targetEntity=Profs::class, mappedBy="id_classe")
     */
    private $profs;

    public function __construct()
    {
        $this->eleves = new ArrayCollection();
        $this->cours = new ArrayCollection();
        $this->profs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomClasse(): ?string
    {
        return $this->nom_classe;
    }

    public function setNomClasse(string $nom_classe): self
    {
        $this->nom_classe = $nom_classe;

        return $this;
    }

    /**
     * @return Collection|eleves[]
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addEleve(eleves $eleve): self
    {
        if (!$this->eleves->contains($eleve)) {
            $this->eleves[] = $eleve;
            $eleve->setIdClasse($this);
        }

        return $this;
    }

    public function removeEleve(eleves $eleve): self
    {
        if ($this->eleves->contains($eleve)) {
            $this->eleves->removeElement($eleve);
            // set the owning side to null (unless already changed)
            if ($eleve->getIdClasse() === $this) {
                $eleve->setIdClasse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|cours[]
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours[] = $cour;
            $cour->setIdClasse($this);
        }

        return $this;
    }

    public function removeCour(cours $cour): self
    {
        if ($this->cours->contains($cour)) {
            $this->cours->removeElement($cour);
            // set the owning side to null (unless already changed)
            if ($cour->getIdClasse() === $this) {
                $cour->setIdClasse(null);
            }
        }

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
            $prof->addIdClasse($this);
        }

        return $this;
    }

    public function removeProf(profs $prof): self
    {
        if ($this->profs->contains($prof)) {
            $this->profs->removeElement($prof);
            $prof->removeIdClasse($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNomClasse();
    }
}
