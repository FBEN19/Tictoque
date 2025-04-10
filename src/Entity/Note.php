<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_utilisateur = null;

    #[ORM\Column]
    private ?int $id_recette = null;

    #[ORM\Column]
    private ?int $note = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_note = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUtilisateur(): ?int
    {
        return $this->id_utilisateur;
    }

    public function setIdUtilisateur(int $id_utilisateur): static
    {
        $this->id_utilisateur = $id_utilisateur;

        return $this;
    }

    public function getIdRecette(): ?int
    {
        return $this->id_recette;
    }

    public function setIdRecette(int $id_recette): static
    {
        $this->id_recette = $id_recette;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getDateNote(): ?\DateTimeInterface
    {
        return $this->date_note;
    }

    public function setDateNote(\DateTimeInterface $date_note): static
    {
        $this->date_note = $date_note;

        return $this;
    }
}
