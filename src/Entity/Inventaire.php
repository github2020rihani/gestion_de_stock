<?php

namespace App\Entity;

use App\Repository\InventaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InventaireRepository::class)
 */
class Inventaire
{
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="inventaires")
     */
    private $addedBy;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalTTC;

    /**
     * @ORM\OneToMany(targetEntity=InventaireArticle::class, mappedBy="inventaire")
     */
    private $inventaireArticles;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filePdf;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileExel;

    public function __construct()
    {
        $this->inventaireArticles = new ArrayCollection();
        $this->totalTTC = 0 ;
        $this->createdAt = new \DateTime('now');
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

    public function getAddedBy(): ?User
    {
        return $this->addedBy;
    }

    public function setAddedBy(?User $addedBy): self
    {
        $this->addedBy = $addedBy;

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
            $inventaireArticle->setInventaire($this);
        }

        return $this;
    }

    public function removeInventaireArticle(InventaireArticle $inventaireArticle): self
    {
        if ($this->inventaireArticles->removeElement($inventaireArticle)) {
            // set the owning side to null (unless already changed)
            if ($inventaireArticle->getInventaire() === $this) {
                $inventaireArticle->setInventaire(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\Datetime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\Datetime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFilePdf(): ?string
    {
        return $this->filePdf;
    }

    public function setFilePdf(string $filePdf): self
    {
        $this->filePdf = $filePdf;

        return $this;
    }

    public function getFileExel(): ?string
    {
        return $this->fileExel;
    }

    public function setFileExel(string $fileExel): self
    {
        $this->fileExel = $fileExel;

        return $this;
    }


}
