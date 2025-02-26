<?php

namespace App\Entity;

use App\Entity\UO;
use App\Entity\CPV;
use App\Entity\Achat;
use App\Entity\Fermeture;
use App\Entity\Formations;
use App\Entity\Parametres;
use App\Entity\DroitsDAcces;
use App\Entity\Fournisseurs;
use App\Entity\Utilisateurs;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ServicesRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ServicesRepository::class)]
class Services
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code_service = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_service = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dcsca = null;

    #[ORM\OneToMany(mappedBy: 'code_service', targetEntity: Fournisseurs::class)]
    private Collection $fournisseurs;

    #[ORM\OneToMany(mappedBy: 'code_service', targetEntity: CPV::class)]
    private Collection $CPVs;

    #[ORM\OneToMany(mappedBy: 'code_service', targetEntity: Achat::class)]
    private Collection $achats;

    #[ORM\OneToMany(mappedBy: 'code_service', targetEntity: DroitsDAcces::class)]
    private Collection $droitsDAcces;

    #[ORM\OneToMany(mappedBy: 'code_service', targetEntity: Formations::class)]
    private Collection $formations;

    #[ORM\OneToMany(mappedBy: 'code_service', targetEntity: Fermeture::class)]
    private Collection $fermetures;

    #[ORM\OneToMany(mappedBy: 'code_service', targetEntity: Parametres::class)]
    private Collection $parametres;

    #[ORM\OneToMany(mappedBy: 'code_service', targetEntity: Utilisateurs::class)]
    private Collection $utilisateurs;
    

    #[ORM\OneToMany(mappedBy: 'code_service', targetEntity: UO::class)]
    private Collection $uOs;



    public function __construct()
    {
        $this->fournisseurs = new ArrayCollection();
        $this->CPVs = new ArrayCollection();
        $this->achats = new ArrayCollection();
        $this->droitsDAcces = new ArrayCollection();
        $this->formations = new ArrayCollection();
        $this->fermetures = new ArrayCollection();
        $this->parametres = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
        $this->uOs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeService(): ?string
    {
        return $this->code_service;
    }

    public function setCodeService(string $code_service): self
    {
        $this->code_service = $code_service;

        return $this;
    }

    public function getNomService(): ?string
    {
        return $this->nom_service;
    }

    public function setNomService(string $nom_service): self
    {
        $this->nom_service = $nom_service;

        return $this;
    }

    public function getDcsca(): ?string
    {
        return $this->dcsca;
    }

    public function setDcsca(?string $dcsca): self
    {
        $this->dcsca = $dcsca;

        return $this;
    }

    /**
     * @return Collection<int, Fournisseurs>
     */
    public function getFournisseurs(): Collection
    {
        return $this->fournisseurs;
    }

    public function addFournisseur(Fournisseurs $fournisseur): self
    {
        if (!$this->fournisseurs->contains($fournisseur)) {
            $this->fournisseurs->add($fournisseur);
            $fournisseur->setCodeService($this);
        }

        return $this;
    }

    public function removeFournisseur(Fournisseurs $fournisseur): self
    {
        if ($this->fournisseurs->removeElement($fournisseur)) {
            // set the owning side to null (unless already changed)
            if ($fournisseur->getCodeService() === $this) {
                $fournisseur->setCodeService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CPV>
     */
    public function getCPVs(): Collection
    {
        return $this->CPVs;
    }

    public function addCPV(CPV $cPV): self
    {
        if (!$this->CPVs->contains($cPV)) {
            $this->CPVs->add($cPV);
            $cPV->setCodeService($this);
        }

        return $this;
    }

    public function removeCPV(CPV $cPV): self
    {
        if ($this->CPVs->removeElement($cPV)) {
            // set the owning side to null (unless already changed)
            if ($cPV->getCodeService() === $this) {
                $cPV->setCodeService(null);
            }
        }

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
            $achat->setCodeService($this);
        }

        return $this;
    }

    public function removeAchat(Achat $achat): self
    {
        if ($this->achats->removeElement($achat)) {
            // set the owning side to null (unless already changed)
            if ($achat->getCodeService() === $this) {
                $achat->setCodeService(null);
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
            $droitsDAcce->setCodeService($this);
        }

        return $this;
    }

    public function removeDroitsDAcce(DroitsDAcces $droitsDAcce): self
    {
        if ($this->droitsDAcces->removeElement($droitsDAcce)) {
            // set the owning side to null (unless already changed)
            if ($droitsDAcce->getCodeService() === $this) {
                $droitsDAcce->setCodeService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Formations>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formations $formation): self
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setCodeService($this);
        }

        return $this;
    }

    public function removeFormation(Formations $formation): self
    {
        if ($this->formations->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getCodeService() === $this) {
                $formation->setCodeService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Fermeture>
     */
    public function getFermetures(): Collection
    {
        return $this->fermetures;
    }

    public function addFermeture(Fermeture $fermeture): self
    {
        if (!$this->fermetures->contains($fermeture)) {
            $this->fermetures->add($fermeture);
            $fermeture->setCodeService($this);
        }

        return $this;
    }

    public function removeFermeture(Fermeture $fermeture): self
    {
        if ($this->fermetures->removeElement($fermeture)) {
            // set the owning side to null (unless already changed)
            if ($fermeture->getCodeService() === $this) {
                $fermeture->setCodeService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Parametres>
     */
    public function getParametres(): Collection
    {
        return $this->parametres;
    }

    public function addParametre(Parametres $parametre): self
    {
        if (!$this->parametres->contains($parametre)) {
            $this->parametres->add($parametre);
            $parametre->setCodeService($this);
        }

        return $this;
    }

    public function removeParametre(Parametres $parametre): self
    {
        if ($this->parametres->removeElement($parametre)) {
            // set the owning side to null (unless already changed)
            if ($parametre->getCodeService() === $this) {
                $parametre->setCodeService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Utilisateurs>
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateurs $utilisateur): self
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
            $utilisateur->setCodeService($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateurs $utilisateur): self
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getCodeService() === $this) {
                $utilisateur->setCodeService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UO>
     */
    public function getUOs(): Collection
    {
        return $this->uOs;
    }

    public function addUO(UO $uO): self
    {
        if (!$this->uOs->contains($uO)) {
            $this->uOs->add($uO);
            $uO->setCodeService($this);
        }

        return $this;
    }

    public function removeUO(UO $uO): self
    {
        if ($this->uOs->removeElement($uO)) {
            // set the owning side to null (unless already changed)
            if ($uO->getCodeService() === $this) {
                $uO->setCodeService(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->code_service.' - '. $this->nom_service;
    }

 
}
