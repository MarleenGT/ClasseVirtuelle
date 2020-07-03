<?php

namespace App\Entity;

use App\Repository\DateArchiveRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DateArchiveRepository::class)
 */
class DateArchive
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date_derniere_archive;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDerniereArchive(): ?DateTimeInterface
    {
        return $this->date_derniere_archive;
    }

    public function setDateDerniereArchive(DateTimeInterface $date_derniere_archive): self
    {
        $this->date_derniere_archive = $date_derniere_archive;

        return $this;
    }
}
