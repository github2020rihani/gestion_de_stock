<?php

namespace App\Entity;

use App\Repository\AchatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AchatRepository::class)
 */
class Achat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Fournisseur::class, inversedBy="achats")
     */
    private $fournisseur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalHT;

    /**
     * @ORM\Column(type="float", length=255, nullable=true)
     */
    private $totalTVA;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $timbre;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalTTC;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $fodec;

    /**
     * @ORM\OneToMany(targetEntity=AchatArticle::class, mappedBy="Achat")
     */
    private $achatArticles;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="achats")
     */
    private $addedBy;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $stocker;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $remise;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $tronsport;

    public function __construct()
    {
        $this->achatArticles = new ArrayCollection();
        $this->setStocker(false);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): self
    {
        $this->fournisseur = $fournisseur;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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

    public function getTotalTVA(): ?float
    {
        return $this->totalTVA;
    }

    public function setTotalTVA(?float $totalTVA): self
    {
        $this->totalTVA = $totalTVA;

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

    public function getTotalTTC(): ?float
    {
        return $this->totalTTC;
    }

    public function setTotalTTC(?float $totalTTC): self
    {
        $this->totalTTC = $totalTTC;

        return $this;
    }

    public function getFodec(): ?bool
    {
        return $this->fodec;
    }

    public function setFodec(?bool $fodec): self
    {
        $this->fodec = $fodec;

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
            $achatArticle->setAchat($this);
        }

        return $this;
    }

    public function removeAchatArticle(AchatArticle $achatArticle): self
    {
        if ($this->achatArticles->removeElement($achatArticle)) {
            // set the owning side to null (unless already changed)
            if ($achatArticle->getAchat() === $this) {
                $achatArticle->setAchat(null);
            }
        }

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

    public function getStocker(): ?bool
    {
        return $this->stocker;
    }

    public function setStocker(?bool $stocker): self
    {
        $this->stocker = $stocker;

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

    public function getTronsport(): ?float
    {
        return $this->tronsport;
    }

    public function setTronsport(?float $tronsport): self
    {
        $this->tronsport = $tronsport;

        return $this;
    }
}
