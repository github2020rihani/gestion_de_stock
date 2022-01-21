<?php


namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @UniqueEntity("email")
 */
class  Client
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $code;


    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Nom est vide")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $prenom;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(name="telephone", type="string", length=20, nullable=true, unique=true)
     *
     * @Assert\Length(
     *     min=8,
     *     max="20",
     *     minMessage="Mobile doit être au moin 8 caractéres.",
     *     maxMessage="Mobile est trop long (max 20 caractéres).",
     *    )
     */
    private $telephone;

    /**
     * @ORM\Column(name="email", type="string", length=255, unique=true, nullable=true)
     * @Assert\Email()
     */
    private $email;


    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $codeTVA;



    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }


    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country" , inversedBy="clients")
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City" , inversedBy="clients" )
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=Devis::class, mappedBy="client")
     */
    private $devis;

    /**
     * @ORM\OneToMany(targetEntity=BondLivraison::class, mappedBy="customer")
     */
    private $bondLivraisons;

    /**
     * @ORM\OneToMany(targetEntity=Invoice::class, mappedBy="customer")
     */
    private $invoices;

    /**
     * @ORM\OneToMany(targetEntity=Avoir::class, mappedBy="customer")
     */
    private $avoirs;



    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->devis = new ArrayCollection();
        $this->bondLivraisons = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->avoirs = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @param mixed $adresse
     */
    public function setAdresse($adresse): void
    {
        $this->adresse = $adresse;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getCodeTVA()
    {
        return $this->codeTVA;
    }

    /**
     * @param mixed $codeTVA
     */
    public function setCodeTVA($codeTVA): void
    {
        $this->codeTVA = $codeTVA;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

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
            $devi->setClient($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): self
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getClient() === $this) {
                $devi->setClient(null);
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
            $bondLivraison->setCustomer($this);
        }

        return $this;
    }

    public function removeBondLivraison(BondLivraison $bondLivraison): self
    {
        if ($this->bondLivraisons->removeElement($bondLivraison)) {
            // set the owning side to null (unless already changed)
            if ($bondLivraison->getCustomer() === $this) {
                $bondLivraison->setCustomer(null);
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
            $invoice->setCustomer($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getCustomer() === $this) {
                $invoice->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Avoir[]
     */
    public function getAvoirs(): Collection
    {
        return $this->avoirs;
    }

    public function addAvoir(Avoir $avoir): self
    {
        if (!$this->avoirs->contains($avoir)) {
            $this->avoirs[] = $avoir;
            $avoir->setCustomer($this);
        }

        return $this;
    }

    public function removeAvoir(Avoir $avoir): self
    {
        if ($this->avoirs->removeElement($avoir)) {
            // set the owning side to null (unless already changed)
            if ($avoir->getCustomer() === $this) {
                $avoir->setCustomer(null);
            }
        }

        return $this;
    }








}
