<?php

namespace App\Entity;

use App\Repository\UORepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: UORepository::class)]
class UO
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code_uo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle_uo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etat_uo = null;

    #[ORM\OneToMany(mappedBy: 'code_uo', targetEntity: Achat::class)]
    private Collection $achats;

    #[ORM\ManyToOne(inversedBy: 'uOs')]
    private ?Services $code_service = null;

    public function __construct()
    {
        $this->achats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeUo(): ?string
    {
        return $this->code_uo;
    }

    public function setCodeUo(string $code_uo): self
    {
        $this->code_uo = $code_uo;

        return $this;
    }

    public function getLibelleUo(): ?string
    {
        return $this->libelle_uo;
    }

    public function setLibelleUo(?string $libelle_uo): self
    {
        $this->libelle_uo = $libelle_uo;

        return $this;
    }

    public function getEtatUo(): ?string
    {
        return $this->etat_uo;
    }

    public function setEtatUo(?string $etat_uo): self
    {
        $this->etat_uo = $etat_uo;

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
            $achat->setCodeUo($this);
        }

        return $this;
    }

    public function removeAchat(Achat $achat): self
    {
        if ($this->achats->removeElement($achat)) {
            // set the owning side to null (unless already changed)
            if ($achat->getCodeUo() === $this) {
                $achat->setCodeUo(null);
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
        return $this->libelle_uo;
    }
}
