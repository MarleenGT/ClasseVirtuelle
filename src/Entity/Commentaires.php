<?php

namespace App\Entity;

use App\Repository\CommentairesRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentairesRepository::class)
 */
class Commentaires
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="commentaires_concernes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_concerne;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="commentaires_ecrits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_auteur;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     */
    private $commentaire;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdConcerne(): ?Users
    {
        return $this->id_concerne;
    }

    public function setIdConcerne(?Users $id_concerne): self
    {
        $this->id_concerne = $id_concerne;

        return $this;
    }

    public function getIdAuteur(): ?Users
    {
        return $this->id_auteur;
    }

    public function setIdAuteur(?Users $id_auteur): self
    {
        $this->id_auteur = $id_auteur;

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

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }
}
