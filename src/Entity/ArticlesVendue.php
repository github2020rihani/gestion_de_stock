<?php

namespace App\Entity;

use App\Repository\ArticlesVendueRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticlesVendueRepository::class)
 */
class ArticlesVendue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articlesVendues")
     */
    private $addedBy;

    /**
     * @ORM\ManyToOne(targetEntity=Invoice::class, inversedBy="articlesVendues")
     */
    private $invoice;

    /**
     * @ORM\ManyToOne(targetEntity=BondLivraison::class, inversedBy="articlesVendues")
     */
    private $bl;



    public function getId(): ?int
    {
        return $this->id;
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

    public function getBl(): ?BondLivraison
    {
        return $this->bl;
    }

    public function setBl(?BondLivraison $bl): self
    {
        $this->bl = $bl;

        return $this;
    }


}
