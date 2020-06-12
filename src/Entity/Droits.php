<?php

namespace App\Entity;

use App\Repository\DroitsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DroitsRepository::class)
 */
class Droits
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
    private $nom_droit;

    /**
     * @ORM\ManyToMany(targetEntity=Roles::class, inversedBy="droits")
     */
    private $id_role;

    public function __construct()
    {
        $this->id_role = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDroit(): ?string
    {
        return $this->nom_droit;
    }

    public function setNomDroit(string $nom_droit): self
    {
        $this->nom_droit = $nom_droit;

        return $this;
    }

    /**
     * @return Collection|Roles[]
     */
    public function getIdRole(): Collection
    {
        return $this->id_role;
    }

    public function addIdRole(Roles $idRole): self
    {
        if (!$this->id_role->contains($idRole)) {
            $this->id_role[] = $idRole;
        }

        return $this;
    }

    public function removeIdRole(Roles $idRole): self
    {
        if ($this->id_role->contains($idRole)) {
            $this->id_role->removeElement($idRole);
        }

        return $this;
    }
}
