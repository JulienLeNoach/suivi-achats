<?php

namespace App\Entity;

use App\Repository\TVARepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TVARepository::class)]
class TVA
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $tva_ident = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tva_lib = null;

    #[ORM\Column(nullable: true)]
    private ?float $tva_taux = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tva_etat = null;

    #[ORM\OneToMany(mappedBy: 'tva_ident', targetEntity: Achat::class)]
    private Collection $achats;

    public function __construct()
    {
        $this->achats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTvaIdent(): ?float
    {
        return $this->tva_ident;
    }

    public function setTvaIdent(float $tva_ident): self
    {
        $this->tva_ident = $tva_ident;

        return $this;
    }

    public function getTvaLib(): ?string
    {
        return $this->tva_lib;
    }

    public function setTvaLib(?string $tva_lib): self
    {
        $this->tva_lib = $tva_lib;

        return $this;
    }

    public function getTvaTaux(): ?float
    {
        return $this->tva_taux;
    }

    public function setTvaTaux(?float $tva_taux): self
    {
        $this->tva_taux = $tva_taux;

        return $this;
    }

    public function getTvaEtat(): ?string
    {
        return $this->tva_etat;
    }

    public function setTvaEtat(?string $tva_etat): self
    {
        $this->tva_etat = $tva_etat;

        return $this;
    }

    /**
     * @return Collection<int, Achat>
     */
    public function getAchats(): Collection
    {
        return $this->achats;
    }

    public function addAchat(Achat $achat): self
    {
        if (!$this->achats->contains($achat)) {
            $this->achats->add($achat);
            $achat->setTvaIdent($this);
        }

        return $this;
    }

    public function removeAchat(Achat $achat): self
    {
        if ($this->achats->removeElement($achat)) {
            // set the owning side to null (unless already changed)
            if ($achat->getTvaIdent() === $this) {
                $achat->setTvaIdent(null);
            }
        }

        return $this;
    }
     public function __toString()
    {
        return $this->tva_lib;
    } 

}
