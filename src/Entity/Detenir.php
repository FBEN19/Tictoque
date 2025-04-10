<?php

namespace App\Entity;

use App\Repository\DetenirRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetenirRepository::class)]
class Detenir
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(length: 50)]
    private ?string $unite = null;

    #[ORM\Column]
    private ?int $id_recette = null;

    #[ORM\Column]
    private ?int $id_ingredient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getUnite(): ?string
    {
        return $this->unite;
    }

    public function setUnite(string $unite): static
    {
        $this->unite = $unite;

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

    public function getIdIngredient(): ?int
    {
        return $this->id_ingredient;
    }

    public function setIdIngredient(int $id_ingredient): static
    {
        $this->id_ingredient = $id_ingredient;

        return $this;
    }
}
