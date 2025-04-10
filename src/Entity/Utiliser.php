<?php

namespace App\Entity;

use App\Repository\UtiliserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtiliserRepository::class)]
#[ORM\Table(name: "utiliser")]
class Utiliser
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Recette::class)]
    #[ORM\JoinColumn(name: "id_recette", referencedColumnName: "id", nullable: false)]
    private ?Recette $recette = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Ustensile::class)]
    #[ORM\JoinColumn(name: "id_ustensile", referencedColumnName: "id", nullable: false)]
    private ?Ustensile $ustensile = null;

    public function getRecette(): ?Recette
    {
        return $this->recette;
    }

    public function setRecette(?Recette $recette): static
    {
        $this->recette = $recette;
        return $this;
    }

    public function getUstensile(): ?Ustensile
    {
        return $this->ustensile;
    }

    public function setUstensile(?Ustensile $ustensile): static
    {
        $this->ustensile = $ustensile;
        return $this;
    }
}