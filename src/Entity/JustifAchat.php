<?php

namespace App\Entity;

use App\Repository\JustifAchatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JustifAchatRepository::class)]
class JustifAchat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $type_justif = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle_justif = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?bool $etat_justif = null;

    #[ORM\OneToMany(mappedBy: 'justifAchat', targetEntity: Achat::class)]
    private Collection $achat;

    public function __construct()
    {
        $this->achat = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeJustif(): ?string
    {
        return $this->type_justif;
    }

    public function setTypeJustif(string $type_justif): static
    {
        $this->type_justif = $type_justif;

        return $this;
    }
    public function getLibelleJustif(): ?string
    {
        return $this->libelle_justif;
    }

    public function setLibelleJustif(string $libelle_justif): static
    {
        $this->libelle_justif = $libelle_justif;

        return $this;
    }

    public function getEtatJustif(): ?bool
    {
        return $this->etat_justif;
    }

    public function setEtatJustif(?bool $etat_justif): self
    {
        $this->etat_justif = $etat_justif;
        return $this;
    }


    /**
     * @return Collection<int, Achat>
     */
    public function getAchat(): Collection
    {
        return $this->achat;
    }

    public function addAchat(Achat $achat): static
    {
        if (!$this->achat->contains($achat)) {
            $this->achat->add($achat);
            $achat->setJustifAchat($this);
        }

        return $this;
    }

    public function removeAchat(Achat $achat): static
    {
        if ($this->achat->removeElement($achat)) {
            // set the owning side to null (unless already changed)
            if ($achat->getJustifAchat() === $this) {
                $achat->setJustifAchat(null);
            }
        }

        return $this;
    }
}
