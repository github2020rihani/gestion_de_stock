<?php

namespace App\Entity;

use App\Repository\ArticleAvoirRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleAvoirRepository::class)
 */
class ArticleAvoir
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=AVOIR::class, inversedBy="articleAvoirs")
     */
    private $avoir;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="articleAvoirs")
     */
    private $article;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $qte;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $pvente;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $total;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $puht;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $puhtnet;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $remise;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $taxe;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalht;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $puttc;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalttc;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvoir(): ?AVOIR
    {
        return $this->avoir;
    }

    public function setAvoir(?AVOIR $avoir): self
    {
        $this->avoir = $avoir;

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

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(?int $qte): self
    {
        $this->qte = $qte;

        return $this;
    }

    public function getPvente(): ?float
    {
        return $this->pvente;
    }

    public function setPvente(?float $pvente): self
    {
        $this->pvente = $pvente;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getPuht(): ?float
    {
        return $this->puht;
    }

    public function setPuht(?float $puht): self
    {
        $this->puht = $puht;

        return $this;
    }

    public function getPuhtnet(): ?float
    {
        return $this->puhtnet;
    }

    public function setPuhtnet(?float $puhtnet): self
    {
        $this->puhtnet = $puhtnet;

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

    public function getTaxe(): ?float
    {
        return $this->taxe;
    }

    public function setTaxe(?float $taxe): self
    {
        $this->taxe = $taxe;

        return $this;
    }

    public function getTotalht(): ?float
    {
        return $this->totalht;
    }

    public function setTotalht(?float $totalht): self
    {
        $this->totalht = $totalht;

        return $this;
    }

    public function getPuttc(): ?float
    {
        return $this->puttc;
    }

    public function setPuttc(?float $puttc): self
    {
        $this->puttc = $puttc;

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
}
