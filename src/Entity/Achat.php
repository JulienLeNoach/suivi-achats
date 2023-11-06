<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AchatRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
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

/**
 * @ORM\Column(type="string", length=6000)
 */
    #[ORM\Column(length: 6000)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?string $numero_achat = null;

    #[ORM\Column]
    #[Groups(['achat:list', 'achat:item'])]
    private ?float $id_demande_achat = null;


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
    private ?string $date_notification = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?string $date_annulation = null;


      /**
     * @ORM\Column(length=255, nullable=true)
     * @Groups({"achat:list", "achat:item"})
     * @Assert\Regex(
     *     pattern="/^[0-9]+$/",
     *     message="Le champ doit contenir uniquement des chiffres."
     * )
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?string $numero_ej = null;

    /**
     * @ORM\Column(length=255, nullable=true)
     * @Groups({"achat:list", "achat:item"})
     * @Assert\Regex(
     *     pattern="/^[A-Za-z0-9\s\-]+$/",
     *     message="Le titre doit contenir uniquement des caractères alphanumériques, espaces et tirets."
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
     *     pattern="/^[0-9]+$/",
     *     message="Le champ doit contenir uniquement des chiffres."
     * )
     */
    #[ORM\Column]
    #[Groups(['achat:list', 'achat:item'])]
    private ?float $montant_achat = null;


        /**
     * @ORM\Column(length=255, nullable=true)
     * @Groups({"achat:list", "achat:item"})
     * @Assert\Regex(
     *     pattern="/^[A-Za-z0-9\s\-]+$/",
     *     message="Le champ doit contenir uniquement des caractères alphanumériques, espaces et tirets."
     * )
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?string $observations = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?string $etat_achat = null;
    

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?string $place = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['achat:list', 'achat:item'])]
    private ?string $devis = null;

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

    

    public function setNumeroAchat()
    {
        // Générez le numéro d'achat basé sur l'année en cours et l'ID
        $anneeEnCours = date('Y');
        $this->numero_achat = $anneeEnCours . '-' . $this->id;
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


    public function getIdDemandeAchat(): ?float
    {
        return $this->id_demande_achat;
    }

    public function setIdDemandeAchat(float $id_demande_achat): self
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

    public function getDateNotification(): ?string
    {
        return $this->date_notification;
    }

    public function setDateNotification(?string $date_notification): self
    {
        $this->date_notification = $date_notification;

        return $this;
    }

    public function getDateAnnulation(): ?string
    {
        return $this->date_annulation;
    }

    public function setDateAnnulation(?string $date_annulation): self
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

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getDevis(): ?string
    {
        return $this->devis;
    }

    public function setDevis(?string $devis): self
    {
        $this->devis = $devis;

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




}
