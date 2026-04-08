<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Entité représentant une page de contenu statique du site.
 *
 * Supporte une hiérarchie parent/enfant et une relation vers une catégorie.
 * Le statut peut valoir : 'brouillon', 'publie', 'archive'.
 */
#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $paragraphes = null;

    /** Slug = URL conviviale ex: "a-propos" — doit être unique */
    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $metaDescription = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    /** Valeurs possibles : 'brouillon', 'publie', 'archive' */
    #[ORM\Column(length: 20)]
    private string $statut = 'brouillon';

    /** Relation récursive : une page peut avoir une page parente */
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    /** @var Collection<int, Page> */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    private Collection $children;

    #[ORM\ManyToOne]
    private ?Galerie $galerie = null;

    #[ORM\ManyToOne(inversedBy: 'pages')]
    private ?CategoriePage $categoriePage = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): static { $this->titre = $titre; return $this; }

    public function getParagraphes(): ?string { return $this->paragraphes; }
    public function setParagraphes(?string $paragraphes): static { $this->paragraphes = $paragraphes; return $this; }

    public function getSlug(): ?string { return $this->slug; }
    public function setSlug(?string $slug): static { $this->slug = $slug; return $this; }

    public function getMetaDescription(): ?string { return $this->metaDescription; }
    public function setMetaDescription(?string $metaDescription): static { $this->metaDescription = $metaDescription; return $this; }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeInterface $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getParent(): ?self { return $this->parent; }
    public function setParent(?self $parent): static { $this->parent = $parent; return $this; }

    /** @return Collection<int, Page> */
    public function getChildren(): Collection { return $this->children; }

    /**
     * Ajoute une page enfant à la collection et maintient la cohérence
     * de la relation bidirectionnelle (enfant pointe vers ce parent).
     */
    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }
        return $this;
    }

    /**
     * Retire une page enfant de la collection et réinitialise son parent
     * si elle pointait encore vers ce parent.
     */
    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child)) {
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }
        return $this;
    }

    public function getGalerie(): ?Galerie { return $this->galerie; }
    public function setGalerie(?Galerie $galerie): static { $this->galerie = $galerie; return $this; }

    public function getCategoriePage(): ?CategoriePage { return $this->categoriePage; }
    public function setCategoriePage(?CategoriePage $categoriePage): static { $this->categoriePage = $categoriePage; return $this; }

    /**
     * Avant l'insertion, génère le slug si aucun n'est défini.
     */
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->generateSlug();
    }

    /**
     * Avant chaque mise à jour, régénère le slug si nécessaire
     * et met à jour la date de modification.
     */
    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->generateSlug();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Génère le slug depuis le titre uniquement si aucun slug n'a été saisi manuellement.
     */
    public function generateSlug(): void
    {
        if (!empty($this->slug)) {
            return;
        }

        // Si le titre est vide/null, le slug reste null
        if (empty($this->titre)) {
            $this->slug = null;
            return;
        }

        $slugger = new AsciiSlugger('fr');
        $this->slug = strtolower($slugger->slug($this->titre));
    }
}
