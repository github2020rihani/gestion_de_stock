<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 */
class Invoice
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
    private $numero;

    /**
     * @ORM\Column(type="float")
     */
    private $timbre;

    /**
     * @ORM\ManyToOne(targetEntity=BondLivraison::class, inversedBy="invoices")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $bonLivraison;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $file;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalTTC;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="invoices")
     */
    private $creadetBy;

    public function __construct()
    {
        $this->status = false;
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

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getTimbre(): ?float
    {
        return $this->timbre;
    }

    public function setTimbre(float $timbre): self
    {
        $this->timbre = $timbre;

        return $this;
    }

    public function getBonLivraison(): ?BondLivraison
    {
        return $this->bonLivraison;
    }

    public function setBonLivraison(?BondLivraison $bonLivraison): self
    {
        $this->bonLivraison = $bonLivraison;

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

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreadetBy(): ?User
    {
        return $this->creadetBy;
    }

    public function setCreadetBy(?User $creadetBy): self
    {
        $this->creadetBy = $creadetBy;

        return $this;
    }
}
