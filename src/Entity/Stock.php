<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StockRepository::class)
 */
class Stock
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $qte;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $qteEntree;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $qteSortie;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateEntree;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="stocks")
     */
    private $article;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(?int $qte): self
    {
        $this->qte = $qte;

        return $this;
    }

    public function getQteEntree(): ?int
    {
        return $this->qteEntree;
    }

    public function setQteEntree(?int $qteEntree): self
    {
        $this->qteEntree = $qteEntree;

        return $this;
    }

    public function getQteSortie(): ?int
    {
        return $this->qteSortie;
    }

    public function setQteSortie(?int $qteSortie): self
    {
        $this->qteSortie = $qteSortie;

        return $this;
    }

    public function getDateEntree(): ?\DateTimeInterface
    {
        return $this->dateEntree;
    }

    public function setDateEntree(?\DateTimeInterface $dateEntree): self
    {
        $this->dateEntree = $dateEntree;

        return $this;
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
}
