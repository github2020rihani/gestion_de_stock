<?php

namespace App\Entity;

use App\Repository\PayemetRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PayemetRepository::class)
 */
class Payemet
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
    private $montant;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $reste;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $retenu;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalttc;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numeroCheque;



    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $file_devi;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $file_bl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $file_invoice;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $typePayement;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="payemets")
     */
    private $addedBy;

    /**
     * @ORM\ManyToOne(targetEntity=Invoice::class, inversedBy="payemets")
     */
    private $invoice;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $fileCheque = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $typesPayements = [];

    /**
     * @ORM\ManyToOne(targetEntity=Depense::class, inversedBy="payemets")
     */
    private $depense;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity=Avoir::class, inversedBy="payemets")
     */
    private $avoir;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(?float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getReste(): ?float
    {
        return $this->reste;
    }

    public function setReste(?float $reste): self
    {
        $this->reste = $reste;

        return $this;
    }

    public function getRetenu(): ?float
    {
        return $this->retenu;
    }

    public function setRetenu(?float $retenu): self
    {
        $this->retenu = $retenu;

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

    public function getNumeroCheque(): ?string
    {
        return $this->numeroCheque;
    }

    public function setNumeroCheque(?string $numeroCheque): self
    {
        $this->numeroCheque = $numeroCheque;

        return $this;
    }



    public function getFileDevi(): ?string
    {
        return $this->file_devi;
    }

    public function setFileDevi(?string $file_devi): self
    {
        $this->file_devi = $file_devi;

        return $this;
    }

    public function getFileBl(): ?string
    {
        return $this->file_bl;
    }

    public function setFileBl(?string $file_bl): self
    {
        $this->file_bl = $file_bl;

        return $this;
    }

    public function getFileInvoice(): ?string
    {
        return $this->file_invoice;
    }

    public function setFileInvoice(?string $file_invoice): self
    {
        $this->file_invoice = $file_invoice;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTypePayement(): ?string
    {
        return $this->typePayement;
    }

    public function setTypePayement(?string $typePayement): self
    {
        $this->typePayement = $typePayement;

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

    public function getAddedBy(): ?User
    {
        return $this->addedBy;
    }

    public function setAddedBy(?User $addedBy): self
    {
        $this->addedBy = $addedBy;

        return $this;
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

    public function getFileCheque(): ?array
    {
        return $this->fileCheque;
    }

    public function setFileCheque(?array $fileCheque): self
    {
        $this->fileCheque = $fileCheque;

        return $this;
    }

    public function getTypesPayements(): ?array
    {
        return $this->typesPayements;
    }

    public function setTypesPayements(?array $typesPayements): self
    {
        $this->typesPayements = $typesPayements;

        return $this;
    }

    public function getDepense(): ?Depense
    {
        return $this->depense;
    }

    public function setDepense(?Depense $depense): self
    {
        $this->depense = $depense;

        return $this;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(string $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getAvoir(): ?Avoir
    {
        return $this->avoir;
    }

    public function setAvoir(?Avoir $avoir): self
    {
        $this->avoir = $avoir;

        return $this;
    }


}
