<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 */
class Invoice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero;

    /**
     * @ORM\Column(type="float")
     */
    private $timbre;

    /**
     * @ORM\ManyToOne(targetEntity=BondLivraison::class, inversedBy="invoices")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $bonLivraison;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $file;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalTTC;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="invoices")
     */
    private $creadetBy;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $existBl;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $typePayement;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="invoices")
     */
    private $customer;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalHt;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalHtnet;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalRemise;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalTva;

    /**
     * @ORM\OneToMany(targetEntity=InvoiceArticle::class, mappedBy="invoice")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $invoiceArticles;

    /**
     * @ORM\OneToMany(targetEntity=History::class, mappedBy="invoice")
     */
    private $histories;

    /**
     * @ORM\OneToMany(targetEntity=ArticlesVendue::class, mappedBy="invoice")
     */
    private $articlesVendues;

    /**
     * @ORM\OneToMany(targetEntity=Payemet::class, mappedBy="invoice")
     */
    private $payemets;

    /**
     * @ORM\OneToMany(targetEntity=Avoir::class, mappedBy="invoice")
     */
    private $avoirs;

    /**
     * @ORM\Column(type="boolean")
     */
    private $avoir;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileAvoir;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $remise;

    public function __construct()
    {
        $this->status = 0;
        $this->createdAt = new \DateTime('now');
        $this->timbre = $_ENV['TIMBRE'];
        $this->invoiceArticles = new ArrayCollection();
        $this->histories = new ArrayCollection();
        $this->articlesVendues = new ArrayCollection();
        $this->payemets = new ArrayCollection();
        $this->avoirs = new ArrayCollection();
        $this->avoir = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getTimbre(): ?float
    {
        return $this->timbre;
    }

    public function setTimbre(float $timbre): self
    {
        $this->timbre = $timbre;

        return $this;
    }

    public function getBonLivraison(): ?BondLivraison
    {
        return $this->bonLivraison;
    }

    public function setBonLivraison(?BondLivraison $bonLivraison): self
    {
        $this->bonLivraison = $bonLivraison;

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

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreadetBy(): ?User
    {
        return $this->creadetBy;
    }

    public function setCreadetBy(?User $creadetBy): self
    {
        $this->creadetBy = $creadetBy;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getExistBl(): ?bool
    {
        return $this->existBl;
    }

    public function setExistBl(?bool $existBl): self
    {
        $this->existBl = $existBl;

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

    public function getCustomer(): ?Client
    {
        return $this->customer;
    }

    public function setCustomer(?Client $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getTotalHt(): ?float
    {
        return $this->totalHt;
    }

    public function setTotalHt(?float $totalHt): self
    {
        $this->totalHt = $totalHt;

        return $this;
    }

    public function getTotalHtnet(): ?float
    {
        return $this->totalHtnet;
    }

    public function setTotalHtnet(?float $totalHtnet): self
    {
        $this->totalHtnet = $totalHtnet;

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

    public function getTotalTva(): ?float
    {
        return $this->totalTva;
    }

    public function setTotalTva(?float $totalTva): self
    {
        $this->totalTva = $totalTva;

        return $this;
    }

    /**
     * @return Collection|InvoiceArticle[]
     */
    public function getInvoiceArticles(): Collection
    {
        return $this->invoiceArticles;
    }

    public function addInvoiceArticle(InvoiceArticle $invoiceArticle): self
    {
        if (!$this->invoiceArticles->contains($invoiceArticle)) {
            $this->invoiceArticles[] = $invoiceArticle;
            $invoiceArticle->setInvoice($this);
        }

        return $this;
    }

    public function removeInvoiceArticle(InvoiceArticle $invoiceArticle): self
    {
        if ($this->invoiceArticles->removeElement($invoiceArticle)) {
            // set the owning side to null (unless already changed)
            if ($invoiceArticle->getInvoice() === $this) {
                $invoiceArticle->setInvoice(null);
            }
        }

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
            $history->setInvoice($this);
        }

        return $this;
    }

    public function removeHistory(History $history): self
    {
        if ($this->histories->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getInvoice() === $this) {
                $history->setInvoice(null);
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
            $articlesVendue->setInvoice($this);
        }

        return $this;
    }

    public function removeArticlesVendue(ArticlesVendue $articlesVendue): self
    {
        if ($this->articlesVendues->removeElement($articlesVendue)) {
            // set the owning side to null (unless already changed)
            if ($articlesVendue->getInvoice() === $this) {
                $articlesVendue->setInvoice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Payemet[]
     */
    public function getPayemets(): Collection
    {
        return $this->payemets;
    }

    public function addPayemet(Payemet $payemet): self
    {
        if (!$this->payemets->contains($payemet)) {
            $this->payemets[] = $payemet;
            $payemet->setInvoice($this);
        }

        return $this;
    }

    public function removePayemet(Payemet $payemet): self
    {
        if ($this->payemets->removeElement($payemet)) {
            // set the owning side to null (unless already changed)
            if ($payemet->getInvoice() === $this) {
                $payemet->setInvoice(null);
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
            $avoir->setInvoice($this);
        }

        return $this;
    }

    public function removeAvoir(Avoir $avoir): self
    {
        if ($this->avoirs->removeElement($avoir)) {
            // set the owning side to null (unless already changed)
            if ($avoir->getInvoice() === $this) {
                $avoir->setInvoice(null);
            }
        }

        return $this;
    }

    public function getAvoir(): ?bool
    {
        return $this->avoir;
    }

    public function setAvoir(bool $avoir): self
    {
        $this->avoir = $avoir;

        return $this;
    }

    public function getFileAvoir(): ?string
    {
        return $this->fileAvoir;
    }

    public function setFileAvoir(string $fileAvoir): self
    {
        $this->fileAvoir = $fileAvoir;

        return $this;
    }

    public function getRemise(): ?float
    {
        return $this->remise;
    }

    public function setRemise(?float $remise): self
    {
        $this->remise = $remise;

        return $this;
    }
}
