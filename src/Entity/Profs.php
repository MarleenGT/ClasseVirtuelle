<?php

namespace App\Entity;

use App\Repository\ProfsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfsRepository::class)
 */
class Profs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Users::class, cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\ManyToMany(targetEntity=Matieres::class, inversedBy="profs", fetch="EAGER")
     */
    private $id_matiere;

    /**
     * @ORM\OneToMany(targetEntity=Cours::class, mappedBy="id_prof")
     */
    private $cours;

    /**
     * @ORM\OneToMany(targetEntity=Archives::class, mappedBy="id_prof")
     */
    private $archives;

    /**
     * @return mixed
     */
    public function getArchives()
    {
        return $this->archives;
    }

    /**
     * @ORM\ManyToMany(targetEntity=Classes::class, inversedBy="profs", fetch="EAGER")
     */
    private $id_classe;

    private $type = 'Professeurs';

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $civilite;

    public function __construct()
    {
        $this->id_matiere = new ArrayCollection();
        $this->cours = new ArrayCollection();
        $this->id_classe = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?Users
    {
        return $this->id_user;
    }

    public function setIdUser(Users $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @return Collection|Matieres[]
     */
    public function getIdMatiere(): Collection
    {
        return $this->id_matiere;
    }

    public function addIdMatiere(Matieres $idMatiere): self
    {
        if (!$this->id_matiere->contains($idMatiere)) {
            $this->id_matiere[] = $idMatiere;
        }

        return $this;
    }

    public function removeIdMatiere(Matieres $idMatiere): self
    {
        if ($this->id_matiere->contains($idMatiere)) {
            $this->id_matiere->removeElement($idMatiere);
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
            $cour->setIdProf($this);
        }

        return $this;
    }

    public function removeCour(cours $cour): self
    {
        if ($this->cours->contains($cour)) {
            $this->cours->removeElement($cour);
            // set the owning side to null (unless already changed)
            if ($cour->getIdProf() === $this) {
                $cour->setIdProf(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Classes[]
     */
    public function getIdClasse(): Collection
    {
        return $this->id_classe;
    }

    public function addIdClasse(Classes $idClasse): self
    {
        if (!$this->id_classe->contains($idClasse)) {
            $this->id_classe[] = $idClasse;
        }

        return $this;
    }

    public function removeIdClasse(Classes $idClasse): self
    {
        if ($this->id_classe->contains($idClasse)) {
            $this->id_classe->removeElement($idClasse);
        }

        return $this;
    }
    public function __toString()
    {
        return 'Profs';
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(?string $civilite): self
    {
        $this->civilite = $civilite;

        return $this;
    }
}
