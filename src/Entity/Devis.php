<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DevisRepository::class)
 */
class Devis
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
     * @ORM\Column(type="datetime")
     */
    private $creadetAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="devis")
     */
    private $creadetBy;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="devis")
     */
    private $client;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalTTC;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $finished;

    /**
     * @ORM\OneToMany(targetEntity=DevisArticle::class, mappedBy="devi")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $devisArticles;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $statusMaj;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $file;

    /**
     * @ORM\OneToMany(targetEntity=BondLivraison::class, mappedBy="devi")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $bondLivraisons;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;


    public function __construct()
    {
        $this->creadetAt = new \DateTime('now');
        $this->status = 0;
        $this->finished = false;
        $this->devisArticles = new ArrayCollection();
        $this->setStatusMaj(false);
        $this->bondLivraisons = new ArrayCollection();
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

    public function getCreadetAt(): ?\DateTimeInterface
    {
        return $this->creadetAt;
    }

    public function setCreadetAt(\DateTimeInterface $creadetAt): self
    {
        $this->creadetAt = $creadetAt;

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * @return Collection|DevisArticle[]
     */
    public function getDevisArticles(): Collection
    {
        return $this->devisArticles;
    }

    public function addDevisArticle(DevisArticle $devisArticle): self
    {
        if (!$this->devisArticles->contains($devisArticle)) {
            $this->devisArticles[] = $devisArticle;
            $devisArticle->setDevi($this);
        }

        return $this;
    }

    public function removeDevisArticle(DevisArticle $devisArticle): self
    {
        if ($this->devisArticles->removeElement($devisArticle)) {
            // set the owning side to null (unless already changed)
            if ($devisArticle->getDevi() === $this) {
                $devisArticle->setDevi(null);
            }
        }

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

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return Collection|BondLivraison[]
     */
    public function getBondLivraisons(): Collection
    {
        return $this->bondLivraisons;
    }

    public function addBondLivraison(BondLivraison $bondLivraison): self
    {
        if (!$this->bondLivraisons->contains($bondLivraison)) {
            $this->bondLivraisons[] = $bondLivraison;
            $bondLivraison->setDevi($this);
        }

        return $this;
    }

    public function removeBondLivraison(BondLivraison $bondLivraison): self
    {
        if ($this->bondLivraisons->removeElement($bondLivraison)) {
            // set the owning side to null (unless already changed)
            if ($bondLivraison->getDevi() === $this) {
                $bondLivraison->setDevi(null);
            }
        }

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;

        return $this;
    }


}
