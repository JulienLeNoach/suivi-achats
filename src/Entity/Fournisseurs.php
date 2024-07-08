<?php

namespace App\Entity;

use App\Repository\FournisseursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FournisseursRepository::class)]
class Fournisseurs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
/**
 * @ORM\Column(length=255)
 * @Assert\Regex(
 *     pattern="/^\d{1,14}$/",
 *     message="Le numéro SIRET doit contenir uniquement des chiffres et avoir un maximum de 14 caractères."
 * )
 */
    #[ORM\Column(length: 255)]
    private ?string $num_siret = null;
/**
 * @ORM\Column(length=255)
 * @Assert\Length(
 *     max=255,
 *     maxMessage="Le nom du fournisseur ne doit pas dépasser 255 caractères."
 * )
 */
    #[ORM\Column(length: 255)]
    private ?string $nom_fournisseur = null;

/**
 * @ORM\Column(length=255, nullable=true)
 * @Assert\Length(
 *     max=5,
 *     maxMessage="Le code postal ne doit pas dépasser 5 caractères."
 * )
 * @Assert\Regex(
 *     pattern="/^\d*$/",
 *     message="Le code postal doit contenir uniquement des chiffres."
 * )
 */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code_postal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?bool $pme = null;
/**
 * @ORM\Column(length=255, nullable=true)
 * @Assert\Length(
 *     max=20,
 *     maxMessage="Le code client ne doit pas dépasser 20 caractères."
 * )
 * @Assert\Regex(
 *     pattern="/^\d*$/",
 *     message="Le code client doit contenir uniquement des chiffres."
 * )
 */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code_client = null;
/**
 * @ORM\Column(length=255, nullable=true)
 * @Assert\Length(
 *     max=10,
 *     maxMessage="Le numéro chorus ne doit pas dépasser 10 caractères."
 * )
 * @Assert\Regex(
 *     pattern="/^\d*$/",
 *     message="Le numéro chorus doit contenir uniquement des chiffres."
 * )
 */
    #[ORM\Column(nullable: true)]
    private ?string $num_chorus_fournisseur = null;
/**
 * @ORM\Column(length=255, nullable=true)
 * @Assert\Length(
 *     max=20,
 *     maxMessage="Le numéro de téléphone ne doit pas dépasser 20 caractères."
 * )
 * @Assert\Regex(
 *     pattern="/^\d*(?:\s*\d*)*$/",
 *     message="Le numéro de téléphone doit contenir uniquement des chiffres."
 * )
 */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tel = null;

/**
 * @ORM\Column(length=255, nullable=true)
 * @Assert\Regex(
 *     pattern="/^[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,}$/",
 *     message="L'adresse email n'est pas valide."
 * )
 */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?bool $etat_fournisseur = null;

    #[ORM\ManyToOne(inversedBy: 'fournisseurs')]
    private ?Services $code_service = null;

    #[ORM\OneToMany(mappedBy: 'num_siret', targetEntity: Achat::class)]
    private Collection $achats;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $date_maj_fournisseur = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumSiret(): ?string
    {
        return $this->num_siret;
    }

    public function setNumSiret(string $num_siret): self
    {
        $this->num_siret = $num_siret;

        return $this;
    }

    public function getNomFournisseur(): ?string
    {
        return $this->nom_fournisseur;
    }

    public function setNomFournisseur(string $nom_fournisseur): self
    {
        $this->nom_fournisseur = $nom_fournisseur;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(?string $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getPme(): ?bool
    {
        return $this->pme;
    }

    public function setPme(?bool $pme): self
    {
        $this->pme = $pme;

        return $this;
    }

    public function getCodeClient(): ?string
    {
        return $this->code_client;
    }

    public function setCodeClient(?string $code_client): self
    {
        $this->code_client = $code_client;

        return $this;
    }

    public function getNumChorusFournisseur(): ?string
    {
        return $this->num_chorus_fournisseur;
    }

    public function setNumChorusFournisseur(?string $num_chorus_fournisseur): self
    {
        $this->num_chorus_fournisseur = $num_chorus_fournisseur;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getFAX(): ?string
    {
        return $this->FAX;
    }

    public function setFAX(?string $FAX): self
    {
        $this->FAX = $FAX;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getEtatFournisseur(): ?bool
    {
        return $this->etat_fournisseur;
    }

    public function setEtatFournisseur(?bool $etat_fournisseur): self
    {
        $this->etat_fournisseur = $etat_fournisseur;

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
            $achat->setNumSiret($this);
        }

        return $this;
    }

    public function removeAchat(Achat $achat): self
    {
        if ($this->achats->removeElement($achat)) {
            // set the owning side to null (unless already changed)
            if ($achat->getNumSiret() === $this) {
                $achat->setNumSiret(null);
            }
        }

        return $this;
    }

    public function getDateMajFournisseur(): ?string
    {
        return $this->date_maj_fournisseur;
    }

    public function setDateMajFournisseur(?string $date_maj_fournisseur): self
    {
        $this->date_maj_fournisseur = $date_maj_fournisseur;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }
    public function __toString()
    {
        return $this->num_siret.'-'. $this->nom_fournisseur;

    } 

}
