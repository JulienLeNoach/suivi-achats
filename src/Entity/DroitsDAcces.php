<?php

namespace App\Entity;

use App\Repository\DroitsDAccesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DroitsDAccesRepository::class)]
class DroitsDAcces
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'droitsDAcces')]
    private ?Services $code_service = null;

    #[ORM\ManyToOne(inversedBy: 'droitsDAcces')]
    private ?Utilisateurs $id_utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'droitsDAcces')]
    private ?OptionsDuMenu $code_de_l_option = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeService(): ?Services
    {
        return $this->code_service;
    }

    public function setCodeService(?Services $code_service): self
    {
        $this->code_service = $code_service;

        return $this;
    }

    public function getIdUtilisateur(): ?Utilisateurs
    {
        return $this->id_utilisateur;
    }

    public function setIdUtilisateur(?Utilisateurs $id_utilisateur): self
    {
        $this->id_utilisateur = $id_utilisateur;

        return $this;
    }

    public function getCodeDeLOption(): ?OptionsDuMenu
    {
        return $this->code_de_l_option;
    }

    public function setCodeDeLOption(?OptionsDuMenu $code_de_l_option): self
    {
        $this->code_de_l_option = $code_de_l_option;

        return $this;
    }
}
