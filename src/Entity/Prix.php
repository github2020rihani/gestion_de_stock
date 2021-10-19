<?php

namespace App\Entity;

use App\Repository\PrixRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PrixRepository::class)
 */
class Prix
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $puAchaHT;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $phAchatTTC;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $puVenteHT;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $tva;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $taux;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="prixes")
     */
    private $article;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="prixes")
     */
    private $addedBy;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $puVenteTTC;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPuAchaHT(): ?float
    {
        return $this->puAchaHT;
    }

    public function setPuAchaHT(?float $puAchaHT): self
    {
        $this->puAchaHT = $puAchaHT;

        return $this;
    }

    public function getPhAchatTTC(): ?float
    {
        return $this->phAchatTTC;
    }

    public function setPhAchatTTC(?float $phAchatTTC): self
    {
        $this->phAchatTTC = $phAchatTTC;

        return $this;
    }

    public function getPuVenteHT(): ?float
    {
        return $this->puVenteHT;
    }

    public function setPuVenteHT(?float $puVenteHT): self
    {
        $this->puVenteHT = $puVenteHT;

        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(?float $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getTaux(): ?float
    {
        return $this->taux;
    }

    public function setTaux(?float $taux): self
    {
        $this->taux = $taux;

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

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPuVenteTTC(): ?float
    {
        return $this->puVenteTTC;
    }

    public function setPuVenteTTC(?float $puVenteTTC): self
    {
        $this->puVenteTTC = $puVenteTTC;

        return $this;
    }
}
