<?php

namespace App\Entity;

use App\Repository\AvoirRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AvoirRepository::class)
 */
class Avoir
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Invoice::class, inversedBy="avoirs")
     */
    private $invoice;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalttc;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="avoirs")
     */
    private $addedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateUsing;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $year;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="avoirs")
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity=ArticleAvoir::class, mappedBy="avoir")
     */
    private $articleAvoirs;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalTva;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalRemise;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalHtnet;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalHt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typePayement;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $timbre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $file;

    /**
     * @ORM\OneToMany(targetEntity=Payemet::class, mappedBy="avoir")
     */
    private $payemets;



    public function __construct()
    {
        $this->status = false;
        $this->createdAt = new \DateTime();
        $this->articleAvoirs = new ArrayCollection();
        $this->payemets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getTotalttc(): ?float
    {
        return $this->totalttc;
    }

    public function setTotalttc(?float $totalttc): self
    {
        $this->totalttc = $totalttc;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDateUsing(): ?\DateTimeInterface
    {
        return $this->dateUsing;
    }

    public function setDateUsing(?\DateTimeInterface $dateUsing): self
    {
        $this->dateUsing = $dateUsing;

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

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;

        return $this;
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

    public function getCustomer(): ?Client
    {
        return $this->customer;
    }

    public function setCustomer(?Client $customer): self
    {
        $this->customer = $customer;

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
            $articleAvoir->setAvoir($this);
        }

        return $this;
    }

    public function removeArticleAvoir(ArticleAvoir $articleAvoir): self
    {
        if ($this->articleAvoirs->removeElement($articleAvoir)) {
            // set the owning side to null (unless already changed)
            if ($articleAvoir->getAvoir() === $this) {
                $articleAvoir->setAvoir(null);
            }
        }

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

    public function getTotalRemise(): ?float
    {
        return $this->totalRemise;
    }

    public function setTotalRemise(?float $totalRemise): self
    {
        $this->totalRemise = $totalRemise;

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

    public function getTotalHt(): ?float
    {
        return $this->totalHt;
    }

    public function setTotalHt(?float $totalHt): self
    {
        $this->totalHt = $totalHt;

        return $this;
    }

    public function getTypePayement(): ?string
    {
        return $this->typePayement;
    }

    public function setTypePayement(string $typePayement): self
    {
        $this->typePayement = $typePayement;

        return $this;
    }

    public function getTimbre(): ?float
    {
        return $this->timbre;
    }

    public function setTimbre(?float $timbre): self
    {
        $this->timbre = $timbre;

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
            $payemet->setAvoir($this);
        }

        return $this;
    }

    public function removePayemet(Payemet $payemet): self
    {
        if ($this->payemets->removeElement($payemet)) {
            // set the owning side to null (unless already changed)
            if ($payemet->getAvoir() === $this) {
                $payemet->setAvoir(null);
            }
        }

        return $this;
    }


}
