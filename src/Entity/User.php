<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_RESPONSABLE = 'ROLE_RESPONSABLE';
    public const ROLE_GERANT = 'ROLE_GERANT';
    public const ROLE_MAGASINIER = 'ROLE_MAGASINIER';
    public const ROLE_PERSONELLE = 'ROLE_PERSONELLE';
    public const ROLE_ACHAT = 'ROLE_ACHAT';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];


    private $plainPassword;
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
    /**
     * @param $password
     */
    public function setPlainPassword($password): void
    {
        $this->plainPassword = $password;
    }


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $matricule;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $restToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $function;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="users")
     */
    private $departemnt;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="addedBy")
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity=AchatArticle::class, mappedBy="addedBy")
     */
    private $achatArticles;

    /**
     * @ORM\OneToMany(targetEntity=Achat::class, mappedBy="addedBy")
     */
    private $achats;

    /**
     * @ORM\OneToMany(targetEntity=Prix::class, mappedBy="addedBy")
     */
    private $prixes;

    /**
     * @ORM\OneToMany(targetEntity=Inventaire::class, mappedBy="addedBy")
     */
    private $inventaires;

    /**
     * @ORM\OneToMany(targetEntity=Devis::class, mappedBy="creadetBy")
     */
    private $devis;

    /**
     * @ORM\OneToMany(targetEntity=BondLivraison::class, mappedBy="createdBy")
     */
    private $bondLivraisons;

    /**
     * @ORM\OneToMany(targetEntity=Invoice::class, mappedBy="creadetBy")
     */
    private $invoices;



    public function __construct()
    {
        $this->status = true;
        $this->setLastLogin(new \DateTime('now'));
        $this->setStatus(1);
        $this->articles = new ArrayCollection();
        $this->achatArticles = new ArrayCollection();
        $this->achats = new ArrayCollection();
        $this->prixes = new ArrayCollection();
        $this->inventaires = new ArrayCollection();
        $this->devis = new ArrayCollection();
        $this->bondLivraisons = new ArrayCollection();
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(?string $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getRestToken(): ?string
    {
        return $this->restToken;
    }

    public function setRestToken(?string $restToken): self
    {
        $this->restToken = $restToken;

        return $this;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function setFunction(string $function): self
    {
        $this->function = $function;

        return $this;
    }

    public function getDepartemnt(): ?Departement
    {
        return $this->departemnt;
    }

    public function setDepartemnt(?Departement $departemnt): self
    {
        $this->departemnt = $departemnt;

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setAddedBy($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAddedBy() === $this) {
                $article->setAddedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AchatArticle[]
     */
    public function getAchatArticles(): Collection
    {
        return $this->achatArticles;
    }

    public function addAchatArticle(AchatArticle $achatArticle): self
    {
        if (!$this->achatArticles->contains($achatArticle)) {
            $this->achatArticles[] = $achatArticle;
            $achatArticle->setAddedBy($this);
        }

        return $this;
    }

    public function removeAchatArticle(AchatArticle $achatArticle): self
    {
        if ($this->achatArticles->removeElement($achatArticle)) {
            // set the owning side to null (unless already changed)
            if ($achatArticle->getAddedBy() === $this) {
                $achatArticle->setAddedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Achat[]
     */
    public function getAchats(): Collection
    {
        return $this->achats;
    }

    public function addAchat(Achat $achat): self
    {
        if (!$this->achats->contains($achat)) {
            $this->achats[] = $achat;
            $achat->setAddedBy($this);
        }

        return $this;
    }

    public function removeAchat(Achat $achat): self
    {
        if ($this->achats->removeElement($achat)) {
            // set the owning side to null (unless already changed)
            if ($achat->getAddedBy() === $this) {
                $achat->setAddedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Prix[]
     */
    public function getPrixes(): Collection
    {
        return $this->prixes;
    }

    public function addPrix(Prix $prix): self
    {
        if (!$this->prixes->contains($prix)) {
            $this->prixes[] = $prix;
            $prix->setAddedBy($this);
        }

        return $this;
    }

    public function removePrix(Prix $prix): self
    {
        if ($this->prixes->removeElement($prix)) {
            // set the owning side to null (unless already changed)
            if ($prix->getAddedBy() === $this) {
                $prix->setAddedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Inventaire[]
     */
    public function getInventaires(): Collection
    {
        return $this->inventaires;
    }

    public function addInventaire(Inventaire $inventaire): self
    {
        if (!$this->inventaires->contains($inventaire)) {
            $this->inventaires[] = $inventaire;
            $inventaire->setAddedBy($this);
        }

        return $this;
    }

    public function removeInventaire(Inventaire $inventaire): self
    {
        if ($this->inventaires->removeElement($inventaire)) {
            // set the owning side to null (unless already changed)
            if ($inventaire->getAddedBy() === $this) {
                $inventaire->setAddedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Devis[]
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): self
    {
        if (!$this->devis->contains($devi)) {
            $this->devis[] = $devi;
            $devi->setCreadetBy($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): self
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getCreadetBy() === $this) {
                $devi->setCreadetBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BondLivraison[]
     */
    public function getBondLivraisons(): Collection
    {
        return $this->bondLivraisons;
    }

    public function addBondLivraison(BondLivraison $bondLivraison): self
    {
        if (!$this->bondLivraisons->contains($bondLivraison)) {
            $this->bondLivraisons[] = $bondLivraison;
            $bondLivraison->setCreatedBy($this);
        }

        return $this;
    }

    public function removeBondLivraison(BondLivraison $bondLivraison): self
    {
        if ($this->bondLivraisons->removeElement($bondLivraison)) {
            // set the owning side to null (unless already changed)
            if ($bondLivraison->getCreatedBy() === $this) {
                $bondLivraison->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Invoice[]
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setCreadetBy($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getCreadetBy() === $this) {
                $invoice->setCreadetBy(null);
            }
        }

        return $this;
    }


}