<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UtilisateursRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
#[ApiResource]
class Utilisateurs implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $nom_connexion = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_utilisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom_utilisateur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $droits_a_toutes_les_fonctions = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $administrateur_central = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?bool $etat_utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'utilisateurs', targetEntity: Achat::class)]
    private Collection $achats;

    #[ORM\OneToMany(mappedBy: 'id_utilisateur', targetEntity: DroitsDAcces::class)]
    private Collection $droitsDAcces;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    private ?Services $code_service = null;
    

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $trigram = null;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Calendar::class)]
    private Collection $calendars;

    public function __construct()
    {
        $this->droitsDAcces = new ArrayCollection();
        $this->calendars = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomConnexion(): ?string
    {
        return $this->nom_connexion;
    }

    public function setNomConnexion(string $nom_connexion): self
    {
        $this->nom_connexion = $nom_connexion;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->nom_connexion;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    public function getNomUtilisateur(): ?string
    {
        return $this->nom_utilisateur;
    }

    public function setNomUtilisateur(string $nom_utilisateur): self
    {
        $this->nom_utilisateur = $nom_utilisateur;

        return $this;
    }

    public function getPrenomUtilisateur(): ?string
    {
        return $this->prenom_utilisateur;
    }

    public function setPrenomUtilisateur(string $prenom_utilisateur): self
    {
        $this->prenom_utilisateur = $prenom_utilisateur;

        return $this;
    }

    public function getDroitsAToutesLesFonctions(): ?bool
    {
        return $this->droits_a_toutes_les_fonctions;
    }

    public function setDroitsAToutesLesFonctions(?bool $droits_a_toutes_les_fonctions): self
    {
        $this->droits_a_toutes_les_fonctions = $droits_a_toutes_les_fonctions;

        return $this;
    }

    public function getAdministrateurCentral(): ?bool
    {
        return $this->administrateur_central;
    }

    public function setAdministrateurCentral(?bool $administrateur_central): self
    {
        $this->administrateur_central = $administrateur_central;

        return $this;
    }

    public function getEtatUtilisateur(): ?bool
    {

        

        return $this->etat_utilisateur;
    }

    public function setEtatUtilisateur(?bool $etat_utilisateur): self
    {
        $this->etat_utilisateur = $etat_utilisateur;

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
            $achat->setUtilisateurs($this);
        }

        return $this;
    }

    public function removeAchat(Achat $achat): self
    {
        if ($this->achats->removeElement($achat)) {
            // set the owning side to null (unless already changed)
            if ($achat->getUtilisateurs() === $this) {
                $achat->setUtilisateurs(null);
            }
        }

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
            $droitsDAcce->setIdUtilisateur($this);
        }

        return $this;
    }

    public function removeDroitsDAcce(DroitsDAcces $droitsDAcce): self
    {
        if ($this->droitsDAcces->removeElement($droitsDAcce)) {
            // set the owning side to null (unless already changed)
            if ($droitsDAcce->getIdUtilisateur() === $this) {
                $droitsDAcce->setIdUtilisateur(null);
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

    public function getTrigram(): ?string
    {
        return $this->trigram;
    }

    public function setTrigram(?string $trigram): self
    {
        $this->trigram = $trigram;

        return $this;
    }


      public function __toString()
    {
        return  $this->trigram.' - '. $this->nom_utilisateur;
    }

      /**
       * @return Collection<int, Calendar>
       */
      public function getCalendars(): Collection
      {
          return $this->calendars;
      }

      public function addCalendar(Calendar $calendar): self
      {
          if (!$this->calendars->contains($calendar)) {
              $this->calendars->add($calendar);
              $calendar->setUserId($this);
          }

          return $this;
      }

      public function removeCalendar(Calendar $calendar): self
      {
          if ($this->calendars->removeElement($calendar)) {
              // set the owning side to null (unless already changed)
              if ($calendar->getUserId() === $this) {
                  $calendar->setUserId(null);
              }
          }

          return $this;
      }  

      public function getUsername(): string
      {
          return (string) $this->nom_connexion;
      }
    
}
