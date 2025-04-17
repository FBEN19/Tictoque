<?php

namespace App\Entity;

use App\Repository\DetenirRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetenirRepository::class)]
#[ORM\Table(name: "detenir")]
class Detenir
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'detenir')]
    #[ORM\JoinColumn(name: "id_recette", referencedColumnName: "id", nullable: false)]
    private ?Recette $recette = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Ingredient::class)]
    #[ORM\JoinColumn(name: "id_ingredient", referencedColumnName: "id", nullable: false)]
    private ?Ingredient $ingredient = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(length: 50)]
    private ?string $unite = null;

    public function getRecette(): ?Recette
    {
        return $this->recette;
    }

    public function setRecette(?Recette $recette): static
    {
        $this->recette = $recette;
        return $this;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): static
    {
        $this->ingredient = $ingredient;
        return $this;
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
}