<?php

namespace App\Entity;

use App\Repository\CPVRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\FormTypeInterface;

#[ORM\Entity(repositoryClass: CPVRepository::class)]
class CPV
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code_cpv = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle_cpv = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mt_cpv = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etat_cpv = null;

    #[ORM\OneToMany(mappedBy: 'code_cpv', targetEntity: Achat::class)]
    private Collection $achats;

    #[ORM\ManyToOne(inversedBy: 'CPVs')]
    private ?Services $code_service = null;

    public function __construct()
    {
        $this->achats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeCpv(): ?string
    {
        return $this->code_cpv;
    }

    public function setCodeCpv(string $code_cpv): self
    {
        $this->code_cpv = $code_cpv;

        return $this;
    }

    public function getLibelleCpv(): ?string
    {
        return $this->libelle_cpv;
    }

    public function setLibelleCpv(?string $libelle_cpv): self
    {
        $this->libelle_cpv = $libelle_cpv;

        return $this;
    }

    public function getMtCpv(): ?string
    {
        return $this->mt_cpv;
    }

    public function setMtCpv(?string $mt_cpv): self
    {
        $this->mt_cpv = $mt_cpv;

        return $this;
    }

    public function getEtatCpv(): ?string
    {
        return $this->etat_cpv;
    }

    public function setEtatCpv(?string $etat_cpv): self
    {
        $this->etat_cpv = $etat_cpv;

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
            $achat->setCodeCpv($this);
        }

        return $this;
    }

    public function removeAchat(Achat $achat): self
    {
        if ($this->achats->removeElement($achat)) {
            // set the owning side to null (unless already changed)
            if ($achat->getCodeCpv() === $this) {
                $achat->setCodeCpv(null);
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
        return $this->libelle_cpv;
    } 
}
