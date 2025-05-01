<?php

namespace App\Entity;

use App\Repository\RecetteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: RecetteRepository::class)]
class Recette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id", nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: Commentaire::class, orphanRemoval: true, cascade: ['remove'])]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: Etape::class, cascade: ['persist', 'remove'])]
    private Collection $etapes;

    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: Detenir::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $detenir;

    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: Utiliser::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $utiliser;

    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: Note::class, cascade: ['remove'])]
    private Collection $notes;

    public function __construct()
    {
        $this->etapes = new ArrayCollection();
        $this->detenir = new ArrayCollection();
        $this->utiliser = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getEtapes(): Collection
    {
        return $this->etapes;
    }

    public function addEtape(Etape $etape): self
    {
        if (!$this->etapes->contains($etape)) {
            $this->etapes[] = $etape;
            $etape->setRecette($this);
        }

        return $this;
    }

    public function removeEtape(Etape $etape): self
    {
        if ($this->etapes->removeElement($etape)) {
            if ($etape->getRecette() === $this) {
                $etape->setRecette(null);
            }
        }

        return $this;
    }

    public function getDetenir(): Collection
    {
        return $this->detenir;
    }

    public function addDetenir(Detenir $d): self
    {
        if (!$this->detenir->contains($d)) {
            $this->detenir[] = $d;
            $d->setRecette($this);
        }

        return $this;
    }

    public function removeDetenir(Detenir $d): self
    {
        if ($this->detenir->removeElement($d)) {
            if ($d->getRecette() === $this) {
                $d->setRecette(null);
            }
        }

        return $this;
    }

    public function getUtiliser(): Collection
    {
        return $this->utiliser;
    }

    public function addUtiliser(Utiliser $u): self
    {
        if (!$this->utiliser->contains($u)) {
            $this->utiliser[] = $u;
            $u->setRecette($this);
        }

        return $this;
    }

    public function removeUtiliser(Utiliser $u): self
    {
        if ($this->utiliser->removeElement($u)) {
            if ($u->getRecette() === $this) {
                $u->setRecette(null);
            }
        }

        return $this;
    }

    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setRecette($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            if ($note->getRecette() === $this) {
                $note->setRecette(null);
            }
        }

        return $this;
    }

    public function getNoteMoyenne(): ?float
    {
        $notes = $this->getNotes();
        if (count($notes) === 0) {
            return 0;
        }

        $somme = 0;
        foreach ($notes as $note) {
            $somme += $note->getNote();
        }

        return round($somme / count($notes), 2);
    }

    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }
    public function getUstensiles(): array
    {
        $ustensiles = [];
        foreach ($this->utiliser as $utilisation) {
            $ustensile = $utilisation->getUstensile();
            if ($ustensile !== null) {
                $ustensiles[] = $ustensile;
            }
        }
        return $ustensiles;
    }

}