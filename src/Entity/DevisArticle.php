<?php

namespace App\Entity;

use App\Repository\DevisArticleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DevisArticleRepository::class)
 */
class DevisArticle
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
     * @ORM\Column(type="float", nullable=true)
     */
    private $total;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $pventettc;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $remise;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="devisArticles")
     */
    private $article;

    /**
     * @ORM\ManyToOne(targetEntity=Devis::class, inversedBy="devisArticles")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $devi;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $statusMaj;
    public function __construct()
    {
        $this->statusMaj = false;
    }

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

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getPventettc(): ?float
    {
        return $this->pventettc;
    }

    public function setPventettc(?float $pventettc): self
    {
        $this->pventettc = $pventettc;

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

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

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

    public function getStatusMaj(): ?bool
    {
        return $this->statusMaj;
    }

    public function setStatusMaj(?bool $statusMaj): self
    {
        $this->statusMaj = $statusMaj;

        return $this;
    }
}
