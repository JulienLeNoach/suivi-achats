<?php

namespace App\Entity;

use App\Repository\AchatCounterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AchatCounterRepository::class)]
class AchatCounter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $year = null;

    #[ORM\Column]
    private ?int $last_number = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getLastNumber(): ?int
    {
        return $this->last_number;
    }

    public function setLastNumber(int $last_number): static
    {
        $this->last_number = $last_number;

        return $this;
    }
}
