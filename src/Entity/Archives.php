<?php

namespace App\Entity;

use App\Repository\ArchivesRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArchivesRepository::class)
 */
class Archives
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Profs::class, inversedBy="archives")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_prof;

    /**
     * @ORM\Column(type="datetime")
     */
    private $heure_debut;

    /**
     * @ORM\Column(type="datetime")
     */
    private $heure_fin;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $commentaire;

    /**
     * @ORM\ManyToOne(targetEntity=Classes::class, inversedBy="archives")
     */
    private $id_classe;

    /**
     * @ORM\ManyToOne(targetEntity=Sousgroupes::class, inversedBy="archives")
     */
    private $id_sousgroupe;

    /**
     * @ORM\ManyToOne(targetEntity=Matieres::class, inversedBy="archives")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_matiere;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProf(): ?int
    {
        return $this->id_prof;
    }

    public function setIdProf(int $id_prof): self
    {
        $this->id_prof = $id_prof;

        return $this;
    }

    public function getHeureDebut(): ?DateTimeInterface
    {
        return $this->heure_debut;
    }

    public function setHeureDebut(DateTimeInterface $heure_debut): self
    {
        $this->heure_debut = $heure_debut;

        return $this;
    }

    public function getHeureFin(): ?DateTimeInterface
    {
        return $this->heure_fin;
    }

    public function setHeureFin(DateTimeInterface $heure_fin): self
    {
        $this->heure_fin = $heure_fin;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getIdClasse(): ?int
    {
        return $this->id_classe;
    }

    public function setIdClasse(?int $id_classe): self
    {
        $this->id_classe = $id_classe;

        return $this;
    }

    public function getIdSousgroupe(): ?int
    {
        return $this->id_sousgroupe;
    }

    public function setIdSousgroupe(?int $id_sousgroupe): self
    {
        $this->id_sousgroupe = $id_sousgroupe;

        return $this;
    }

    public function getIdMatiere(): ?int
    {
        return $this->id_matiere;
    }

    public function setIdMatiere(int $id_matiere): self
    {
        $this->id_matiere = $id_matiere;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
