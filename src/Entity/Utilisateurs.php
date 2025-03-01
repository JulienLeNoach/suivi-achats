<?php

namespace App\Entity;

use App\Entity\DroitsDAcces;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\UtilisateursRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
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
/**
 * @ORM\Column(length=255)
 * @Assert\Length(
 *     max=255,
 *     maxMessage="Le nom de l'utilisateur ne doit pas dépasser 255 caractères."
 * )
 */
    #[ORM\Column(length: 255)]
    private ?string $nom_utilisateur = null;
/**
 * @ORM\Column(length=255)
 * @Assert\Length(
 *     max=255,
 *     maxMessage="Le prenom de l'utilisateur ne doit pas dépasser 255 caractères."
 * )
 */
    #[ORM\Column(length: 255)]
    private ?string $prenom_utilisateur = null;



    #[ORM\Column(type: 'boolean')]
    private ?bool $administrateur_central = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?bool $etat_utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'utilisateurs', targetEntity: Achat::class)]
    private Collection $achats;

    #[ORM\OneToMany(mappedBy: 'id_utilisateur', targetEntity: DroitsDAcces::class)]
    private Collection $droitsDAcces;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    private ?Services $code_service = null;
    
/**
 * @ORM\Column(length=3)
 * @Assert\Length(
 *     max=3,
 *     maxMessage="Le trigram de l'utilisateur ne doit pas dépasser 3 caractères."
 * )
 */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $trigram = null;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Calendar::class)]
    private Collection $calendars;
    
    #[ORM\Column(type: 'boolean')]
    private ?bool $isAdmin = false;
    
    // ...
    
    public function getIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }
    
    public function setIsAdmin(?bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;
    
        return $this;
    }
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
    if (!in_array('ROLE_USER', $roles, true)) {
        $roles[] = 'ROLE_USER';
    }

    if ($this->isAdmin) {
        $roles[] = 'ROLE_ADMIN';
    }


    return array_unique($roles);
}

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    /**
     * Ajoute un rôle à l'utilisateur s'il n'est pas déjà présent.
     *
     * @param string $role Le rôle à ajouter
     * @return self
     */
    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }
    /**
 * Supprime un rôle de l'utilisateur.
 *
 * @param string $role Le rôle à supprimer
 * @return self
 */
public function removeRole(string $role): self
{
    $key = array_search($role, $this->roles);
    if ($key !== false) {
        unset($this->roles[$key]);
    }

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
      public function hashPassword(UserPasswordHasherInterface $hasher): void
      {
          $this->password = $hasher->hashPassword($this, $this->password);
      }
}
