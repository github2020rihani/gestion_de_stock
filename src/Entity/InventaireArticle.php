<?php

namespace App\Entity;

use App\Repository\InventaireArticleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InventaireArticleRepository::class)
 */
class InventaireArticle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="inventaireArticles")
     */
    private $article;

    /**
     * @ORM\ManyToOne(targetEntity=Inventaire::class, inversedBy="inventaireArticles")
     */
    private $inventaire;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prAchatHT;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prAchatTTC;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $qte;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalTTC;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getInventaire(): ?Inventaire
    {
        return $this->inventaire;
    }

    public function setInventaire(?Inventaire $inventaire): self
    {
        $this->inventaire = $inventaire;

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

    public function getPrAchatHT(): ?float
    {
        return $this->prAchatHT;
    }

    public function setPrAchatHT(?float $prAchatHT): self
    {
        $this->prAchatHT = $prAchatHT;

        return $this;
    }

    public function getPrAchatTTC(): ?float
    {
        return $this->prAchatTTC;
    }

    public function setPrAchatTTC(?float $prAchatTTC): self
    {
        $this->prAchatTTC = $prAchatTTC;

        return $this;
    }

    public function getQte(): ?float
    {
        return $this->qte;
    }

    public function setQte(?float $qte): self
    {
        $this->qte = $qte;

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
}
