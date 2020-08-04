<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 */
class Users implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $identifiant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mdp;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=Roles::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_role;

    /**
     * @ORM\OneToMany(targetEntity=Commentaires::class, mappedBy="id_concerne")
     */
    private $commentaires_concernes;

    /**
     * @ORM\OneToMany(targetEntity=Commentaires::class, mappedBy="id_auteur")
     */
    private $commentaires_ecrits;

    /**
     * @ORM\OneToMany(targetEntity=Sousgroupes::class, mappedBy="id_createur")
     */
    private $sousgroupes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif = 0;

    /**
     * @ORM\OneToOne(targetEntity=Admins::class, mappedBy="id_user", cascade={"persist", "remove"})
     */
    private $admins;

    /**
     * @ORM\ManyToMany(targetEntity=Sousgroupes::class, mappedBy="Visibilite", fetch="EAGER")
     */
    private $sousgroupes_visibles;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    public function __construct()
    {
        $this->commentaires_concernes = new ArrayCollection();
        $this->commentaires_ecrits = new ArrayCollection();
        $this->sousgroupes = new ArrayCollection();
        $this->sousgroupes_visibles = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(string $identifiant): self
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): self
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getIdRole(): ?Roles
    {
        return $this->id_role;
    }

    public function setIdRole(?Roles $id_role): self
    {
        $this->id_role = $id_role;

        return $this;
    }

    /**
     * @return Collection|commentaires[]
     */
    public function getCommentairesConcernes(): Collection
    {
        return $this->commentaires_concernes;
    }

    public function addCommentairesConcerne(Commentaires $commentairesConcerne): self
    {
        if (!$this->commentaires_concernes->contains($commentairesConcerne)) {
            $this->commentaires_concernes[] = $commentairesConcerne;
            $commentairesConcerne->setIdConcerne($this);
        }

        return $this;
    }

    public function removeCommentairesConcerne(Commentaires $commentairesConcerne): self
    {
        if ($this->commentaires_concernes->contains($commentairesConcerne)) {
            $this->commentaires_concernes->removeElement($commentairesConcerne);
            // set the owning side to null (unless already changed)
            if ($commentairesConcerne->getIdConcerne() === $this) {
                $commentairesConcerne->setIdConcerne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commentaires[]
     */
    public function getCommentairesEcrits(): Collection
    {
        return $this->commentaires_ecrits;
    }

    public function addCommentairesEcrit(Commentaires $commentairesEcrit): self
    {
        if (!$this->commentaires_ecrits->contains($commentairesEcrit)) {
            $this->commentaires_ecrits[] = $commentairesEcrit;
            $commentairesEcrit->setIdAuteur($this);
        }

        return $this;
    }

    public function removeCommentairesEcrit(Commentaires $commentairesEcrit): self
    {
        if ($this->commentaires_ecrits->contains($commentairesEcrit)) {
            $this->commentaires_ecrits->removeElement($commentairesEcrit);
            // set the owning side to null (unless already changed)
            if ($commentairesEcrit->getIdAuteur() === $this) {
                $commentairesEcrit->setIdAuteur(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Sousgroupes[]
     */
    public function getSousgroupes(): Collection
    {
        return $this->sousgroupes;
    }

    public function addSousgroupe(Sousgroupes $sousgroupe): self
    {
        if (!$this->sousgroupes->contains($sousgroupe)) {
            $this->sousgroupes[] = $sousgroupe;
            $sousgroupe->setIdCreateur($this);
        }

        return $this;
    }

    public function removeSousgroupe(Sousgroupes $sousgroupe): self
    {
        if ($this->sousgroupes->contains($sousgroupe)) {
            $this->sousgroupes->removeElement($sousgroupe);
            // set the owning side to null (unless already changed)
            if ($sousgroupe->getIdCreateur() === $this) {
                $sousgroupe->setIdCreateur(null);
            }
        }

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function __toString()
    {
        return 'users';
    }

    public function getRoles()
    {
        return [$this->getIdRole()->getNomRole()];
    }

    public function getPassword()
    {
        return $this->getMdp();
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername()
    {
       return $this->getIdentifiant();
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getAdmins(): ?Admins
    {
        return $this->admins;
    }

    public function setAdmins(Admins $admins): self
    {
        $this->admins = $admins;

        // set the owning side of the relation if necessary
        if ($admins->getIdUser() !== $this) {
            $admins->setIdUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Sousgroupes[]
     */
    public function getSousgroupesVisibles(): Collection
    {
        return $this->sousgroupes_visibles;
    }

    public function addSousgroupesVisible(Sousgroupes $sousgroupesVisible): self
    {
        if (!$this->sousgroupes_visibles->contains($sousgroupesVisible)) {
            $this->sousgroupes_visibles[] = $sousgroupesVisible;
            $sousgroupesVisible->addVisibilite($this);
        }

        return $this;
    }

    public function removeSousgroupesVisible(Sousgroupes $sousgroupesVisible): self
    {
        if ($this->sousgroupes_visibles->contains($sousgroupesVisible)) {
            $this->sousgroupes_visibles->removeElement($sousgroupesVisible);
            $sousgroupesVisible->removeVisibilite($this);
        }

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }
}
