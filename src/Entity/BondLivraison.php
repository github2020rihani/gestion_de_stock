<?php

namespace App\Entity;

use App\Repository\BondLivraisonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BondLivraisonRepository::class)
 */
class BondLivraison
{
    public const PAYEMENT_CARTE = 'PAYEMENT_CARTE';
    public const PAYEMENT_ESPACE = 'PAYEMENT_ESPACE';
    public const PAYEMENT_CHEQUE = 'PAYEMENT_CHEQUE';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numero;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bondLivraisons")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="bondLivraisons")
     */
    private $customer;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $typePayement;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $existDevi;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalTTC;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalHT;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalHTNET;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalRemise;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $totalTVA;

    /**
     * @ORM\OneToMany(targetEntity=BonlivraisonArticle::class, mappedBy="bonLivraison")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $bonlivraisonArticles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $file;

    /**
     * @ORM\OneToMany(targetEntity=Invoice::class, mappedBy="bonLivraison")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $invoices;

    /**
     * @ORM\ManyToOne(targetEntity=Devis::class, inversedBy="bondLivraisons")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $devi;

    /**
     * @ORM\Column(type="integer")
     */
    private $year;

    /**
     * @ORM\OneToMany(targetEntity=History::class, mappedBy="bl")
     */
    private $histories;

    /**
     * @ORM\OneToMany(targetEntity=ArticlesVendue::class, mappedBy="bl")
     */
    private $articlesVendues;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->status = 0;
        $this->existDevi = false ;
        $this->bonlivraisonArticles = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->histories = new ArrayCollection();
        $this->articlesVendues = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCustomer(): ?Client
    {
        return $this->customer;
    }

    public function setCustomer(?Client $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getTypePayement(): ?int
    {
        return $this->typePayement;
    }

    public function setTypePayement(?int $typePayement): self
    {
        $this->typePayement = $typePayement;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getExistDevi(): ?bool
    {
        return $this->existDevi;
    }

    public function setExistDevi(bool $existDevi): self
    {
        $this->existDevi = $existDevi;

        return $this;
    }

    public function getTotalTTC(): ?float
    {
        return $this->totalTTC;
    }

    public function setTotalTTC(?float $totalTTC): self
    {
        $this->totalTTC = $totalTTC;

        return $this;
    }

    public function getTotalHT(): ?float
    {
        return $this->totalHT;
    }

    public function setTotalHT(?float $totalHT): self
    {
        $this->totalHT = $totalHT;

        return $this;
    }

    public function getTotalHTNET(): ?float
    {
        return $this->totalHTNET;
    }

    public function setTotalHTNET(?float $totalHTNET): self
    {
        $this->totalHTNET = $totalHTNET;

        return $this;
    }

    public function getTotalRemise(): ?float
    {
        return $this->totalRemise;
    }

    public function setTotalRemise(?float $totalRemise): self
    {
        $this->totalRemise = $totalRemise;

        return $this;
    }

    public function getTotalTVA(): ?string
    {
        return $this->totalTVA;
    }

    public function setTotalTVA(string $totalTVA): self
    {
        $this->totalTVA = $totalTVA;

        return $this;
    }

    /**
     * @return Collection|BonlivraisonArticle[]
     */
    public function getBonlivraisonArticles(): Collection
    {
        return $this->bonlivraisonArticles;
    }

    public function addBonlivraisonArticle(BonlivraisonArticle $bonlivraisonArticle): self
    {
        if (!$this->bonlivraisonArticles->contains($bonlivraisonArticle)) {
            $this->bonlivraisonArticles[] = $bonlivraisonArticle;
            $bonlivraisonArticle->setBonLivraison($this);
        }

        return $this;
    }

    public function removeBonlivraisonArticle(BonlivraisonArticle $bonlivraisonArticle): self
    {
        if ($this->bonlivraisonArticles->removeElement($bonlivraisonArticle)) {
            // set the owning side to null (unless already changed)
            if ($bonlivraisonArticle->getBonLivraison() === $this) {
                $bonlivraisonArticle->setBonLivraison(null);
            }
        }

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

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
            $invoice->setBonLivraison($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getBonLivraison() === $this) {
                $invoice->setBonLivraison(null);
            }
        }

        return $this;
    }

    public function getDevi(): ?Devis
    {
        return $this->devi;
    }

    public function setDevi(?Devis $devi): self
    {
        $this->devi = $devi;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return Collection|History[]
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(History $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
            $history->setBl($this);
        }

        return $this;
    }

    public function removeHistory(History $history): self
    {
        if ($this->histories->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getBl() === $this) {
                $history->setBl(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ArticlesVendue[]
     */
    public function getArticlesVendues(): Collection
    {
        return $this->articlesVendues;
    }

    public function addArticlesVendue(ArticlesVendue $articlesVendue): self
    {
        if (!$this->articlesVendues->contains($articlesVendue)) {
            $this->articlesVendues[] = $articlesVendue;
            $articlesVendue->setBl($this);
        }

        return $this;
    }

    public function removeArticlesVendue(ArticlesVendue $articlesVendue): self
    {
        if ($this->articlesVendues->removeElement($articlesVendue)) {
            // set the owning side to null (unless already changed)
            if ($articlesVendue->getBl() === $this) {
                $articlesVendue->setBl(null);
            }
        }

        return $this;
    }
}
