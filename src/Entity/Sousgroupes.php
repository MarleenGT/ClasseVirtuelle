<?php

namespace App\Entity;

use App\Repository\SousgroupesRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SousgroupesRepository::class)
 */
class Sousgroupes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="sousgroupes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_createur;

    /**
     * @ORM\OneToMany(targetEntity=Cours::class, mappedBy="id_sousgroupe")
     */
    private $cours;

    /**
     * @ORM\OneToMany(targetEntity=Archives::class, mappedBy="id_sousgroupe")
     */
    private $archives;

    /**
     * @ORM\ManyToMany(targetEntity=Eleves::class, mappedBy="id_sousgroupe")
     */
    private $eleves;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom_sousgroupe;

    /**
     * @ORM\ManyToMany(targetEntity=Users::class, inversedBy="sousgroupes_visibles")
     */
    private $Visibilite;


    /**
     * @ORM\Column(type="date")
     */
    private $date_creation;


    public function __construct()
    {
        $this->cours = new ArrayCollection();
        $this->eleves = new ArrayCollection();
        $this->Visibilite = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCreateur(): ?Users
    {
        return $this->id_createur;
    }

    public function setIdCreateur(?Users $id_createur): self
    {
        $this->id_createur = $id_createur;

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
            $cour->setIdSousgroupe($this);
        }

        return $this;
    }

    public function removeCour(cours $cour): self
    {
        if ($this->cours->contains($cour)) {
            $this->cours->removeElement($cour);
            // set the owning side to null (unless already changed)
            if ($cour->getIdSousgroupe() === $this) {
                $cour->setIdSousgroupe(null);
            }
        }

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
            $eleve->addIdSousgroupe($this);
        }

        return $this;
    }

    public function removeEleve(eleves $eleve): self
    {
        if ($this->eleves->contains($eleve)) {
            $this->eleves->removeElement($eleve);
            $eleve->removeIdSousgroupe($this);
        }

        return $this;
    }

    public function getNomSousgroupe(): ?string
    {
        return $this->nom_sousgroupe;
    }

    public function setNomSousgroupe(string $nom_sousgroupe): self
    {
        $this->nom_sousgroupe = $nom_sousgroupe;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getArchives()
    {
        return $this->archives;
    }

    /**
     * @return Collection|Users[]
     */
    public function getVisibilite(): Collection
    {
        return $this->Visibilite;
    }

    public function addVisibilite(Users $visibilite): self
    {
        if (!$this->Visibilite->contains($visibilite)) {
            $this->Visibilite[] = $visibilite;
        }

        return $this;
    }

    public function removeVisibilite(Users $visibilite): self
    {
        if ($this->Visibilite->contains($visibilite)) {
            $this->Visibilite->removeElement($visibilite);
        }

        return $this;
    }

    public function getDateCreation(): ?DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

}
