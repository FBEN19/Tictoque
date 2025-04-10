<?php

namespace App\Entity;

use App\Repository\UtiliserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtiliserRepository::class)]
class Utiliser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_recette = null;

    #[ORM\Column]
    private ?int $id_ustensile = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdUstensile(): ?int
    {
        return $this->id_ustensile;
    }

    public function setIdUstensile(int $id_ustensile): static
    {
        $this->id_ustensile = $id_ustensile;

        return $this;
    }
}
