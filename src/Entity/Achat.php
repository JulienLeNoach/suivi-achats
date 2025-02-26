<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AchatRepository;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AchatRepository::class)]
#[ApiResource]
class Achat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['achat:list', 'achat:item'])]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?\DateTime $date_saisie = null;

    #[ORM\Column(length: 255)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?string $numero_achat = null;

    /**
     * @ORM\Column(type="string", length=8)
     * @Groups({"achat:list", "achat:item"})
     * @Assert\Regex(
     *     pattern="/^\d{8}$/",
     *     message="L'ID de la demande d'achat doit contenir exactement 8 chiffres."
     * )
     */
    #[ORM\Column(type: 'string', length: 8)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?string $id_demande_achat = null;


    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?\DateTime $date_sillage = null;

    #[ORM\Column(length: 255, name: 'date_commande_chorus')]
    #[Groups(['achat:list', 'achat:item'])]
    private ?\DateTime $date_commande_chorus = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?\DateTime $date_valid_inter = null;

    #[ORM\Column(length: 255)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?\DateTime $date_validation = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?\DateTime $date_notification = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?\DateTime $date_annulation = null;


/**
 * @ORM\Column(length=255, nullable=true)
 * @Groups({"achat:list", "achat:item"})
 * @Assert\Regex(
 *     pattern="/^[a-zA-Z0-9]+$/",
 *     message="Le numéro EJ doit contenir uniquement des caractères alphanumériques."
 * )
 * @Assert\Length(
 *     min=10,
 *     max=10,
 *     maxMessage="Le numéro EJ ne doit pas dépasser 10 caractères."
 * )
 */
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?string $numero_ej = null;

/**
 * @ORM\Column(length=255, nullable=true)
 * @Groups({"achat:list", "achat:item"})
 * @Assert\Regex(
 *     pattern="/^[a-zA-Z0-9À-ÿ '\s_\-]+$/u",
 *     message="L'objet d'achat ne doit contenir que des valeurs alphanumériques."
 * )
 * @Assert\Length(
 *     max=100,
 *     maxMessage="L'objet d'achat ne doit pas dépasser 100 caractères."
 * )
 */
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?string $objet_achat = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?string $type_marche = null;


/**
 * @ORM\Column(type="float", nullable=true)
 * @Groups({"achat:list", "achat:item"})
 * @Assert\Regex(
 *     pattern="/^\d+(?:[\.,]\d+)?$/",
 *     message="Le champ doit contenir uniquement des chiffres positifs et peut inclure un point pour les décimales."
 * )
 * @Assert\Length(
 *     max=13,
 *     maxMessage="Le champ ne peut pas dépasser 13 caractères au total."
 * )
 */
#[ORM\Column]
#[Groups(['achat:list', 'achat:item'])]
private ?float $montant_achat = null;

/**
 * @ORM\Column(length=255, nullable=true)
 * @Groups({"achat:list", "achat:item"})
 * @Assert\Regex(
 *     pattern="/^[a-zA-Z0-9À-ÿ '\s_\-]+$/u",
 *     message="L'observation ne doit contenir que des valeurs alphanumériques, des espaces et des retours à la ligne."
 * )
 * @Assert\Length(
 *     max=400,
 *     maxMessage="L'observation ne doit pas dépasser 400 caractères."
 * )    
 */
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?string $observations = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?string $etat_achat = null;
    



    #[ORM\ManyToOne(inversedBy: 'achats')]
    private ?Utilisateurs $utilisateurs = null;

    #[ORM\ManyToOne(inversedBy: 'achats')]
    private ?CPV $code_cpv = null;

    #[ORM\ManyToOne(inversedBy: 'achats')]
    private ?Fournisseurs $num_siret = null;

    #[ORM\ManyToOne(inversedBy: 'achats')]
    private ?Services $code_service = null;

    #[ORM\ManyToOne(inversedBy: 'achats')]
    private ?Formations $code_formation = null;

    #[ORM\ManyToOne(inversedBy: 'achats')]
    private ?UO $code_uo = null;

    #[ORM\ManyToOne(inversedBy: 'achats')]
    private ?TVA $tva_ident = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numero_marche = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numero_ej_marche = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $commentaire_annulation = null;

    #[ORM\OneToMany(mappedBy: 'achat', targetEntity: Devis::class)]
    private Collection $devis;

    #[ORM\ManyToOne(inversedBy: 'achat')]
    private ?JustifAchat $justifAchat = null;

    #[ORM\ManyToOne(inversedBy: 'achat')]
    private ?GSBDD $gsbdd = null;

    public function __construct()
    {
        $this->devis = new ArrayCollection();
    }

    

    public function setNumeroAchat(string $numero_achat)
    {
        // Générez le numéro d'achat basé sur l'année en cours et l'ID
        $this->numero_achat = $numero_achat;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateSaisie(): ?\DateTime
    {
        return $this->date_saisie;
    }

    public function setDateSaisie(?\DateTime $date_saisie): self
    {
        $this->date_saisie = $date_saisie;

        return $this;
    }

    public function getNumeroAchat(): ?string
    {
        return $this->numero_achat;
    }


    public function getIdDemandeAchat(): ?string
    {
        return $this->id_demande_achat;
    }

    public function setIdDemandeAchat(string $id_demande_achat): self
    {
        $this->id_demande_achat = $id_demande_achat;

        return $this;
    }

    public function getDateSillage(): ?\DateTime
    {
        return $this->date_sillage;
    }

    public function setDateSillage(?\DateTime $date_sillage): self
    {
        $this->date_sillage = $date_sillage;

        return $this;
    }

    public function getDateCommandeChorus(): ?\DateTime
    {
        return $this->date_commande_chorus;
    }

    public function setDateCommandeChorus(?\DateTime $date_commande_chorus): self
    {
        $this->date_commande_chorus = $date_commande_chorus;

        return $this;
    }

    public function getDateValidInter(): ?\DateTime
    {
        return $this->date_valid_inter;
    }

    public function setDateValidInter(\DateTime $date_valid_inter): self
    {
        $this->date_valid_inter = $date_valid_inter;

        return $this;
    }

    public function getDateValidation(): ?\DateTime
    {
        return $this->date_validation;
    }

    public function setDateValidation(?\DateTime $date_validation): self
    {
        $this->date_validation = $date_validation;

        return $this;
    }

    public function getDateNotification(): ?\DateTime
    {
        return $this->date_notification;
    }

    public function setDateNotification(?\DateTime $date_notification): self
    {
        $this->date_notification = $date_notification;
        
        return $this;
    }

    public function getDateAnnulation(): \DateTime
    {
        return $this->date_annulation;
    }

    public function setDateAnnulation(\DateTime $date_annulation): self
    {
        $this->date_annulation = $date_annulation;

        return $this;
    }

    public function getNumeroEj(): ?string
    {
        return $this->numero_ej;
    }

    public function setNumeroEj(?string $numero_ej): self
    {
        $this->numero_ej = $numero_ej;

        return $this;
    }

    public function getObjetAchat(): ?string
    {
        return $this->objet_achat;
    }

    public function setObjetAchat(?string $objet_achat): self
    {
        $this->objet_achat = $objet_achat;

        return $this;
    }

    public function getTypeMarche(): ?string
    {
        return $this->type_marche;
    }

    public function setTypeMarche(?string $type_marche): self
    {
        $this->type_marche = $type_marche;

        return $this;
    }

    public function getMontantAchat(): ?float
    {
        return $this->montant_achat;
    }

    public function setMontantAchat(float $montant_achat): self
    {
        $this->montant_achat = $montant_achat;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(?string $observations): self
    {
        $this->observations = $observations;

        return $this;
    }

    public function getEtatAchat(): ?string
    {
        return $this->etat_achat;
    }

    public function setEtatAchat(?string $etat_achat): self
    {
        $this->etat_achat = $etat_achat;

        return $this;
    }

    public function getUtilisateurs(): ?Utilisateurs
    {
        return $this->utilisateurs;
    }

    public function setUtilisateurs(?Utilisateurs $utilisateurs): self
    {
        $this->utilisateurs = $utilisateurs;

        return $this;
    }

    public function getCodeCpv(): ?CPV
    {
        return $this->code_cpv;
    }

    public function setCodeCpv(?CPV $code_cpv): self
    {
        $this->code_cpv = $code_cpv;

        return $this;
    }

    public function getNumSiret(): ?Fournisseurs
    {
        return $this->num_siret;
    }

    public function setNumSiret(?Fournisseurs $num_siret): self
    {
        $this->num_siret = $num_siret;

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

    public function getCodeFormation(): ?Formations
    {
        return $this->code_formation;
    }

    public function setCodeFormation(?Formations $code_formation): self
    {
        $this->code_formation = $code_formation;

        return $this;
    }

    public function getCodeUo(): ?UO
    {
        return $this->code_uo;
    }

    public function setCodeUo(?UO $code_uo): self
    {
        $this->code_uo = $code_uo;

        return $this;
    }

    public function getTvaIdent(): ?TVA
    {
        return $this->tva_ident;
    }

    public function setTvaIdent(?TVA $tva_ident): self
    {
        $this->tva_ident = $tva_ident;

        return $this;
    }

    public function getNumeroMarche(): ?string
    {
        return $this->numero_marche;
    }

    public function setNumeroMarche(?string $numero_marche): static
    {
        $this->numero_marche = $numero_marche;

        return $this;
    }

    public function getNumeroEjMarche(): ?string
    {
        return $this->numero_ej_marche;
    }

    public function setNumeroEjMarche(?string $numero_ej_marche): static
    {
        $this->numero_ej_marche = $numero_ej_marche;

        return $this;
    }

    public function getCommentaireAnnulation(): ?string
    {
        return $this->commentaire_annulation;
    }

    public function setCommentaireAnnulation(?string $commentaire_annulation): static
    {
        $this->commentaire_annulation = $commentaire_annulation;

        return $this;
    }

    /**
     * @return Collection<int, Devis>
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): static
    {
        if (!$this->devis->contains($devi)) {
            $this->devis->add($devi);
            $devi->setAchat($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): static
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getAchat() === $this) {
                $devi->setAchat(null);
            }
        }

        return $this;
    }

    public function getJustifAchat(): ?JustifAchat
    {
        return $this->justifAchat;
    }

    public function setJustifAchat(?JustifAchat $justifAchat): static
    {
        $this->justifAchat = $justifAchat;

        return $this;
    }

    public function getGSBDD(): ?GSBDD
    {
        return $this->gsbdd;
    }

    public function setGSBDD(?GSBDD $gsbdd): static
    {
        $this->gsbdd = $gsbdd;

        return $this;
    }

 




}
