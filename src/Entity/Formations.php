<?php

namespace App\Entity;

use App\Entity\Achat;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FormationsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: FormationsRepository::class)]
class Formations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code_formation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle_formation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?bool $etat_formation = null;

    #[ORM\OneToMany(mappedBy: 'code_formation', targetEntity: Achat::class)]
    private Collection $achats;

    #[ORM\ManyToOne(inversedBy: 'formations')]
    private ?Services $code_service = null;

    public function __construct()
    {
        $this->achats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeFormation(): ?string
    {
        return $this->code_formation;
    }

    public function setCodeFormation(string $code_formation): self
    {
        $this->code_formation = $code_formation;

        return $this;
    }

    public function getLibelleFormation(): ?string
    {
        return $this->libelle_formation;
    }

    public function setLibelleFormation(?string $libelle_formation): self
    {
        $this->libelle_formation = $libelle_formation;

        return $this;
    }

    public function getEtatFormation(): ?bool
    {
        return $this->etat_formation;
    }

    public function setEtatFormation(?bool $etat_formation): self
    {
        $this->etat_formation = $etat_formation;

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
            $achat->setCodeFormation($this);
        }

        return $this;
    }

    public function removeAchat(Achat $achat): self
    {
        if ($this->achats->removeElement($achat)) {
            // set the owning side to null (unless already changed)
            if ($achat->getCodeFormation() === $this) {
                $achat->setCodeFormation(null);
            }
        }

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

    public function __toString()
    {
        return $this->code_formation . ' - '. $this->libelle_formation ;
    }
}
