<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
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
    private $ref;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @return mixed
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @param mixed $ref
     */
    public function setRef($ref): void
    {
        $this->ref = $ref;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $qte;

    /**
     * @ORM\Column(type="float" , nullable=true)
     */
    private $tva;

    /**
     * @ORM\Column(type="float" , nullable=true)
     */
    private $puTTC;

    /**
     * @ORM\Column(type="float" , nullable=true)
     */
    private $marge;

    /**
     * @ORM\Column(type="float" , nullable=true)
     */
    private $prixVente;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     */
    private $addedBy;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="articles")
     */
    private $departement;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="articles")
     */
    private $categorie;

    /**
     * @ORM\OneToMany(targetEntity=AchatArticle::class, mappedBy="article")
     */
    private $achatArticles;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->achatArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPuTTC(): ?float
    {
        return $this->puTTC;
    }

    public function setPuTTC(float $puTTC): self
    {
        $this->puTTC = $puTTC;

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

    public function getPrixVente(): ?float
    {
        return $this->prixVente;
    }

    public function setPrixVente(float $prixVente): self
    {
        $this->prixVente = $prixVente;

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

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getCategorie(): ?Category
    {
        return $this->categorie;
    }

    public function setCategorie(?Category $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection|AchatArticle[]
     */
    public function getAchatArticles(): Collection
    {
        return $this->achatArticles;
    }

    public function addAchatArticle(AchatArticle $achatArticle): self
    {
        if (!$this->achatArticles->contains($achatArticle)) {
            $this->achatArticles[] = $achatArticle;
            $achatArticle->setArticle($this);
        }

        return $this;
    }

    public function removeAchatArticle(AchatArticle $achatArticle): self
    {
        if ($this->achatArticles->removeElement($achatArticle)) {
            // set the owning side to null (unless already changed)
            if ($achatArticle->getArticle() === $this) {
                $achatArticle->setArticle(null);
            }
        }

        return $this;
    }
}
