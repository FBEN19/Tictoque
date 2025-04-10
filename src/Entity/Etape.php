<?php

namespace App\Entity;

use App\Repository\EtapeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtapeRepository::class)]
class Etape
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Jointure avec Recette
    #[ORM\ManyToOne(targetEntity: Recette::class)]
    #[ORM\JoinColumn(name: "id_recette", referencedColumnName: "id", nullable: false)]
    private ?Recette $recette = null;

    #[ORM\Column]
    private ?int $numero_etape = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecette(): ?Recette
    {
        return $this->recette;
    }

    public function setRecette(Recette $recette): static
    {
        $this->recette = $recette;

        return $this;
    }

    public function getNumeroEtape(): ?int
    {
        return $this->numero_etape;
    }

    public function setNumeroEtape(int $numero_etape): static
    {
        $this->numero_etape = $numero_etape;

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
}