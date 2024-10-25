<?php

// src/Entity/CPV.php

namespace App\Entity;

use App\Entity\Services;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CPVRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CPVRepository::class)]
class CPV
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    /**
     * @ORM\Column(length=8, nullable=true)
     * @Assert\Length(
     *     max=8,
     *     maxMessage="Le code du CPV ne doit pas dépasser 8 caractères."
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9_-]*$/",
     *     message="Le code du CPV ne doit contenir que des lettres, chiffres, tirets (-) ou soulignements (_)."
     * )
     */
    #[ORM\Column(length: 8, nullable: true)]
    private ?string $code_cpv = null;
/**
 * @ORM\Column(length=255)
 * @Assert\Length(
 *     max=255,
 *     maxMessage="Le nom du CPV ne doit pas dépasser 255 caractères."
 * )
 */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle_cpv = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etat_cpv = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $mt_cpv_auto = null;

    #[ORM\ManyToOne(inversedBy: 'cpv')]
    private ?Services $code_service = null;

    #[ORM\Column(type: 'float', nullable: true)]  // Ajout du champ premier_seuil
    private ?float $premier_seuil = null;

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

    public function getEtatCpv(): ?bool
    {
        return $this->etat_cpv;
    }

    public function setEtatCpv(?bool $etat_cpv): self
    {
        $this->etat_cpv = $etat_cpv;
        return $this;
    }

    public function getMtCpvAuto(): ?float
    {
        return $this->mt_cpv_auto;
    }

    public function setMtCpvAuto(float $mt_cpv_auto): static
    {
        $this->mt_cpv_auto = $mt_cpv_auto;
        return $this;
    }

    public function getPremierSeuil(): ?float
    {
        return $this->premier_seuil;
    }

    public function setPremierSeuil(?float $premier_seuil): self
    {
        $this->premier_seuil = $premier_seuil;
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
    // Méthode __toString modifiée
    public function __toString(): string
    {
        return $this->code_cpv . ' - '. $this->libelle_cpv ;
    }
}

