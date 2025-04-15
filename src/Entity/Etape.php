<?php
namespace App\Entity;

use App\Repository\EtapeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtapeRepository::class)]
#[ORM\Table(name: 'etape')]
class Etape
{
    // Clé primaire 1/2
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    // Clé primaire 2/2 + clé étrangère vers recette
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Recette::class, inversedBy: 'etapes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recette $recette = null;

    // Numéro d’ordre dans la recette
    #[ORM\Column(type: 'integer')]
    private ?int $numeroEtape = null;

    // Description de l’étape
    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    // === GETTERS / SETTERS ===

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getRecette(): ?Recette
    {
        return $this->recette;
    }

    public function setRecette(?Recette $recette): static
    {
        $this->recette = $recette;
        return $this;
    }

    public function getNumeroEtape(): ?int
    {
        return $this->numeroEtape;
    }

    public function setNumeroEtape(int $numeroEtape): static
    {
        $this->numeroEtape = $numeroEtape;
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