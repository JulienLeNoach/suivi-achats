<?php

namespace App\Entity;

use App\Repository\ParametresRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParametresRepository::class)]
class Parametres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $four1 = null;

    #[ORM\Column(nullable: true)]
    private ?float $four2 = null;

    #[ORM\Column(nullable: true)]
    private ?float $four3 = null;

    #[ORM\Column(nullable: true)]
    private ?float $four4 = null;

    #[ORM\ManyToOne(inversedBy: 'parametres')]
    private ?Services $code_service = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFour1(): ?float
    {
        return $this->four1;
    }

    public function setFour1(?float $four1): self
    {
        $this->four1 = $four1;

        return $this;
    }

    public function getFour2(): ?float
    {
        return $this->four2;
    }

    public function setFour2(?float $four2): self
    {
        $this->four2 = $four2;

        return $this;
    }

    public function getFour3(): ?float
    {
        return $this->four3;
    }

    public function setFour3(?float $four3): self
    {
        $this->four3 = $four3;

        return $this;
    }

    public function getFour4(): ?float
    {
        return $this->four4;
    }

    public function setFour4(?float $four4): self
    {
        $this->four4 = $four4;

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
