<?php

namespace App\Entity;

use App\Repository\FermetureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FermetureRepository::class)]
class Fermeture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fermedate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fermetype = null;

    #[ORM\ManyToOne(inversedBy: 'fermetures')]
    private ?Services $code_service = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFermedate(): ?string
    {
        return $this->fermedate;
    }

    public function setFermedate(string $fermedate): self
    {
        $this->fermedate = $fermedate;

        return $this;
    }

    public function getFermetype(): ?string
    {
        return $this->fermetype;
    }

    public function setFermetype(?string $fermetype): self
    {
        $this->fermetype = $fermetype;

        return $this;
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
}
