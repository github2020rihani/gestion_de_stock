<?php

namespace App\Entity;

use App\Repository\AchatArticleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AchatArticleRepository::class)
 */
class AchatArticle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $puhtnet;

    /**
     * @ORM\Column(type="integer")
     */
    private $qte;

    /**
     * @ORM\Column(type="float")
     */
    private $tva;

    /**
     * @ORM\Column(type="float")
     */
    private $puttc;

    /**
     * @ORM\Column(type="float")
     */
    private $marge;

    /**
     * @ORM\Column(type="float")
     */
    private $pventettc;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="achatArticles")
     */
    private $article;

    /**
     * @ORM\ManyToOne(targetEntity=Achat::class, inversedBy="achatArticles")
     */
    private $Achat;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="achatArticles")
     */
    private $addedBy;

    /**
     * @ORM\Column(type="string", length=255, nullable= true)
     */
    private $typePrix;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $pventeHT;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPuhtnet(): ?float
    {
        return $this->puhtnet;
    }

    public function setPuhtnet(float $puhtnet): self
    {
        $this->puhtnet = $puhtnet;

        return $this;
    }

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(int $qte): self
    {
        $this->qte = $qte;

        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(float $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getPuttc(): ?float
    {
        return $this->puttc;
    }

    public function setPuttc(float $puttc): self
    {
        $this->puttc = $puttc;

        return $this;
    }

    public function getMarge(): ?float
    {
        return $this->marge;
    }

    public function setMarge(float $marge): self
    {
        $this->marge = $marge;

        return $this;
    }

    public function getPventettc(): ?float
    {
        return $this->pventettc;
    }

    public function setPventettc(float $pventettc): self
    {
        $this->pventettc = $pventettc;

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

    public function getAchat(): ?Achat
    {
        return $this->Achat;
    }

    public function setAchat(?Achat $Achat): self
    {
        $this->Achat = $Achat;

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

    public function getAddedBy(): ?User
    {
        return $this->addedBy;
    }

    public function setAddedBy(?User $addedBy): self
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    public function getTypePrix(): ?string
    {
        return $this->typePrix;
    }

    public function setTypePrix(string $typePrix): self
    {
        $this->typePrix = $typePrix;

        return $this;
    }

    public function getPventeHT(): ?float
    {
        return $this->pventeHT;
    }

    public function setPventeHT(?float $pventeHT): self
    {
        $this->pventeHT = $pventeHT;

        return $this;
    }
}
