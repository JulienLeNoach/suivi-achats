<?php

namespace App\Entity;

use App\Repository\GSBDDRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GSBDDRepository::class)]
class GSBDD
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle_gsbdd = null;

    #[ORM\ManyToOne(inversedBy: 'gSBDDs')]
    private ?Achat $achat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleGsbdd(): ?string
    {
        return $this->libelle_gsbdd;
    }

    public function setLibelleGsbdd(string $libelle_gsbdd): static
    {
        $this->libelle_gsbdd = $libelle_gsbdd;

        return $this;
    }

    public function getAchat(): ?Achat
    {
        return $this->achat;
    }

    public function setAchat(?Achat $achat): static
    {
        $this->achat = $achat;

        return $this;
    }
}
