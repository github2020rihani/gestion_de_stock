<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
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
    private $ref;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;


    /**
     * @return mixed
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @param mixed $ref
     */
    public function setRef($ref): void
    {
        $this->ref = $ref;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }




    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     */
    private $addedBy;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="articles")
     */
    private $departement;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="articles")
     */
    private $categorie;

    /**
     * @ORM\OneToMany(targetEntity=AchatArticle::class, mappedBy="article")
     */
    private $achatArticles;

    /**
     * @ORM\OneToMany(targetEntity=Stock::class, mappedBy="article")
     */
    private $stocks;

    /**
     * @ORM\OneToMany(targetEntity=Prix::class, mappedBy="article")
     */
    private $prixes;

    /**
     * @ORM\OneToMany(targetEntity=InventaireArticle::class, mappedBy="article")
     */
    private $inventaireArticles;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $remise;

    /**
     * @ORM\OneToMany(targetEntity=DevisArticle::class, mappedBy="article")
     */
    private $devisArticles;

    /**
     * @ORM\OneToMany(targetEntity=BonlivraisonArticle::class, mappedBy="article")
     */
    private $qte;

    /**
     * @ORM\Column(type="boolean")
     */
    private $stocked;

    /**
     * @ORM\OneToMany(targetEntity=InvoiceArticle::class, mappedBy="article")
     */
    private $invoiceArticles;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $qteReserved;

    /**
     * @ORM\OneToMany(targetEntity=ArticleAvoir::class, mappedBy="article")
     */
    private $articleAvoirs;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->achatArticles = new ArrayCollection();
        $this->stocks = new ArrayCollection();
        $this->prixes = new ArrayCollection();
        $this->inventaireArticles = new ArrayCollection();
        $this->devisArticles = new ArrayCollection();
        $this->qte = new ArrayCollection();
        $this->stocked = false;
        $this->invoiceArticles = new ArrayCollection();
        $this->remise = 0 ;
        $this->articleAvoirs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAddedBy(): ?User
    {
        return $this->addedBy;
    }

    public function setAddedBy(?User $addedBy): self
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getCategorie(): ?Category
    {
        return $this->categorie;
    }

    public function setCategorie(?Category $categorie): self
    {
        $this->categorie = $categorie;

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
            $achatArticle->setArticle($this);
        }

        return $this;
    }

    public function removeAchatArticle(AchatArticle $achatArticle): self
    {
        if ($this->achatArticles->removeElement($achatArticle)) {
            // set the owning side to null (unless already changed)
            if ($achatArticle->getArticle() === $this) {
                $achatArticle->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Stock[]
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stock $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks[] = $stock;
            $stock->setArticle($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): self
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getArticle() === $this) {
                $stock->setArticle(null);
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
            $prix->setArticle($this);
        }

        return $this;
    }

    public function removePrix(Prix $prix): self
    {
        if ($this->prixes->removeElement($prix)) {
            // set the owning side to null (unless already changed)
            if ($prix->getArticle() === $this) {
                $prix->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|InventaireArticle[]
     */
    public function getInventaireArticles(): Collection
    {
        return $this->inventaireArticles;
    }

    public function addInventaireArticle(InventaireArticle $inventaireArticle): self
    {
        if (!$this->inventaireArticles->contains($inventaireArticle)) {
            $this->inventaireArticles[] = $inventaireArticle;
            $inventaireArticle->setArticle($this);
        }

        return $this;
    }

    public function removeInventaireArticle(InventaireArticle $inventaireArticle): self
    {
        if ($this->inventaireArticles->removeElement($inventaireArticle)) {
            // set the owning side to null (unless already changed)
            if ($inventaireArticle->getArticle() === $this) {
                $inventaireArticle->setArticle(null);
            }
        }

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

    /**
     * @return Collection|DevisArticle[]
     */
    public function getDevisArticles(): Collection
    {
        return $this->devisArticles;
    }

    public function addDevisArticle(DevisArticle $devisArticle): self
    {
        if (!$this->devisArticles->contains($devisArticle)) {
            $this->devisArticles[] = $devisArticle;
            $devisArticle->setArticle($this);
        }

        return $this;
    }

    public function removeDevisArticle(DevisArticle $devisArticle): self
    {
        if ($this->devisArticles->removeElement($devisArticle)) {
            // set the owning side to null (unless already changed)
            if ($devisArticle->getArticle() === $this) {
                $devisArticle->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BonlivraisonArticle[]
     */
    public function getQte(): Collection
    {
        return $this->qte;
    }

    public function addQte(BonlivraisonArticle $qte): self
    {
        if (!$this->qte->contains($qte)) {
            $this->qte[] = $qte;
            $qte->setArticle($this);
        }

        return $this;
    }

    public function removeQte(BonlivraisonArticle $qte): self
    {
        if ($this->qte->removeElement($qte)) {
            // set the owning side to null (unless already changed)
            if ($qte->getArticle() === $this) {
                $qte->setArticle(null);
            }
        }

        return $this;
    }

    public function getStocked(): ?bool
    {
        return $this->stocked;
    }

    public function setStocked(bool $stocked): self
    {
        $this->stocked = $stocked;

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
            $invoiceArticle->setArticle($this);
        }

        return $this;
    }

    public function removeInvoiceArticle(InvoiceArticle $invoiceArticle): self
    {
        if ($this->invoiceArticles->removeElement($invoiceArticle)) {
            // set the owning side to null (unless already changed)
            if ($invoiceArticle->getArticle() === $this) {
                $invoiceArticle->setArticle(null);
            }
        }

        return $this;
    }

    public function getQteReserved(): ?int
    {
        return $this->qteReserved;
    }

    public function setQteReserved(int $qteReserved): self
    {
        $this->qteReserved = $qteReserved;

        return $this;
    }

    /**
     * @return Collection|ArticleAvoir[]
     */
    public function getArticleAvoirs(): Collection
    {
        return $this->articleAvoirs;
    }

    public function addArticleAvoir(ArticleAvoir $articleAvoir): self
    {
        if (!$this->articleAvoirs->contains($articleAvoir)) {
            $this->articleAvoirs[] = $articleAvoir;
            $articleAvoir->setArticle($this);
        }

        return $this;
    }

    public function removeArticleAvoir(ArticleAvoir $articleAvoir): self
    {
        if ($this->articleAvoirs->removeElement($articleAvoir)) {
            // set the owning side to null (unless already changed)
            if ($articleAvoir->getArticle() === $this) {
                $articleAvoir->setArticle(null);
            }
        }

        return $this;
    }
}
