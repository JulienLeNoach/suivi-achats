<?php

namespace App\Entity;

use App\Repository\GSBDDRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(mappedBy: 'gSBDD', targetEntity: Achat::class)]
    private Collection $achat;

    #[ORM\ManyToOne(inversedBy: 'fournisseurs')]
    private ?Services $code_service = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?bool $etat_gsbdd = null;

    public function __construct()
    {
        $this->achat = new ArrayCollection();
    }

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

    public function getCodeService(): ?Services
    {
        return $this->code_service;
    }

    public function setCodeService(?Services $code_service): self
    {
        $this->code_service = $code_service;

        return $this;
    }
    public function getEtatGsbdd(): ?bool
    {
        return $this->etat_gsbdd;
    }

    public function setEtatGsbdd(?bool $etat_gsbdd): self
    {
        $this->etat_gsbdd = $etat_gsbdd;

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
            $achat->setGSBDD($this);
        }

        return $this;
    }

    public function removeAchat(Achat $achat): static
    {
        if ($this->achat->removeElement($achat)) {
            // set the owning side to null (unless already changed)
            if ($achat->getGSBDD() === $this) {
                $achat->setGSBDD(null);
            }
        }

        return $this;
    }
}
