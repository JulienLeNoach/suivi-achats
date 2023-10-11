<?php

namespace App\Entity;

use App\Repository\OptionsDuMenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionsDuMenuRepository::class)]
class OptionsDuMenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code_de_l_option = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle_option = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fonction_associee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $option_associee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type_option = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $rang_option = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $option_admin = null;

    #[ORM\OneToMany(mappedBy: 'code_de_l_option', targetEntity: DroitsDAcces::class)]
    private Collection $droitsDAcces;

    public function __construct()
    {
        $this->droitsDAcces = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeDeLOption(): ?string
    {
        return $this->code_de_l_option;
    }

    public function setCodeDeLOption(string $code_de_l_option): self
    {
        $this->code_de_l_option = $code_de_l_option;

        return $this;
    }

    public function getLibelleOption(): ?string
    {
        return $this->libelle_option;
    }

    public function setLibelleOption(?string $libelle_option): self
    {
        $this->libelle_option = $libelle_option;

        return $this;
    }

    public function getFonctionAssociee(): ?string
    {
        return $this->fonction_associee;
    }

    public function setFonctionAssociee(?string $fonction_associee): self
    {
        $this->fonction_associee = $fonction_associee;

        return $this;
    }

    public function getOptionAssociee(): ?string
    {
        return $this->option_associee;
    }

    public function setOptionAssociee(?string $option_associee): self
    {
        $this->option_associee = $option_associee;

        return $this;
    }

    public function getTypeOption(): ?string
    {
        return $this->type_option;
    }

    public function setTypeOption(?string $type_option): self
    {
        $this->type_option = $type_option;

        return $this;
    }

    public function getRangOption(): ?int
    {
        return $this->rang_option;
    }

    public function setRangOption(?int $rang_option): self
    {
        $this->rang_option = $rang_option;

        return $this;
    }

    public function getOptionAdmin(): ?string
    {
        return $this->option_admin;
    }

    public function setOptionAdmin(?string $option_admin): self
    {
        $this->option_admin = $option_admin;

        return $this;
    }

    /**
     * @return Collection<int, DroitsDAcces>
     */
    public function getDroitsDAcces(): Collection
    {
        return $this->droitsDAcces;
    }

    public function addDroitsDAcce(DroitsDAcces $droitsDAcce): self
    {
        if (!$this->droitsDAcces->contains($droitsDAcce)) {
            $this->droitsDAcces->add($droitsDAcce);
            $droitsDAcce->setCodeDeLOption($this);
        }

        return $this;
    }

    public function removeDroitsDAcce(DroitsDAcces $droitsDAcce): self
    {
        if ($this->droitsDAcces->removeElement($droitsDAcce)) {
            // set the owning side to null (unless already changed)
            if ($droitsDAcce->getCodeDeLOption() === $this) {
                $droitsDAcce->setCodeDeLOption(null);
            }
        }

        return $this;
    }


}
