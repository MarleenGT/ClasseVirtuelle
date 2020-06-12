<?php

namespace App\Entity;

use App\Repository\ElevesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ElevesRepository::class)
 */
class Eleves
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Users::class, cascade={"persist", "remove"})
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
     * @ORM\ManyToOne(targetEntity=Classes::class, inversedBy="eleves")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_classe;

    /**
     * @ORM\ManyToMany(targetEntity=Sousgroupes::class, inversedBy="eleves")
     */
    private $id_sousgroupe;

    public function __construct()
    {
        $this->id_sousgroupe = new ArrayCollection();
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

    public function getIdClasse(): ?Classes
    {
        return $this->id_classe;
    }

    public function setIdClasse(?Classes $id_classe): self
    {
        $this->id_classe = $id_classe;

        return $this;
    }

    /**
     * @return Collection|Sousgroupes[]
     */
    public function getIdSousgroupe(): Collection
    {
        return $this->id_sousgroupe;
    }

    public function addIdSousgroupe(Sousgroupes $idSousgroupe): self
    {
        if (!$this->id_sousgroupe->contains($idSousgroupe)) {
            $this->id_sousgroupe[] = $idSousgroupe;
        }

        return $this;
    }

    public function removeIdSousgroupe(Sousgroupes $idSousgroupe): self
    {
        if ($this->id_sousgroupe->contains($idSousgroupe)) {
            $this->id_sousgroupe->removeElement($idSousgroupe);
        }

        return $this;
    }

    public function __toString()
    {
        return 'eleves';
    }
}
