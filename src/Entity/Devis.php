<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        max: 150,
        maxMessage: "Le nom du candidat ne peut pas dépasser 150 caractères."
    )]
    private ?string $nom_candidat = null;

    #[ORM\Column]
    #[Assert\Regex(
        pattern: "/^\d+$/",
        message: "Le montant HT doit contenir uniquement des chiffres."
    )]
    private ?int $montant_ht = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 250,
        maxMessage: "L'observation ne peut pas dépasser 250 caractères."
    )]
    private ?string $obs = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    private ?Achat $achat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCandidat(): ?string
    {
        return $this->nom_candidat;
    }

    public function setNomCandidat(string $nom_candidat): static
    {
        $this->nom_candidat = $nom_candidat;

        return $this;
    }

    public function getMontantHt(): ?int
    {
        return $this->montant_ht;
    }

    public function setMontantHt(int $montant_ht): static
    {
        $this->montant_ht = $montant_ht;

        return $this;
    }

    public function getObs(): ?string
    {
        return $this->obs;
    }

    public function setObs(?string $obs): static
    {
        $this->obs = $obs;

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
