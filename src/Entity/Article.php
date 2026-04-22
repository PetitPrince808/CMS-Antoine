<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePublication = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $metaDescription = null;

    /** Valeurs possibles : 'brouillon', 'publie', 'archive' */
    #[ORM\Column(length: 20)]
    private string $statut = 'brouillon';

    /** Slug = URL conviviale ex: "mon-article" — doit être unique */
    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?CategorieArticle $categorieArticle = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'articles')]
    private Collection $tags;

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'article', orphanRemoval: true)]
    private Collection $commentaires;

    #[ORM\ManyToOne]
    private ?User $auteur = null;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitre(): ?string { return $this->titre; }

    public function setTitre(string $titre): static { $this->titre = $titre; return $this; }

    public function getContenu(): ?string { return $this->contenu; }

    public function setContenu(?string $contenu): static { $this->contenu = $contenu; return $this; }

    public function getDatePublication(): ?\DateTimeInterface { return $this->datePublication; }

    public function setDatePublication(?\DateTimeInterface $datePublication): static { $this->datePublication = $datePublication; return $this; }

    public function getMetaDescription(): ?string { return $this->metaDescription; }

    public function setMetaDescription(?string $metaDescription): static { $this->metaDescription = $metaDescription; return $this; }

    public function getStatut(): string { return $this->statut; }

    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getCategorieArticle(): ?CategorieArticle { return $this->categorieArticle; }

    public function setCategorieArticle(?CategorieArticle $categorieArticle): static { $this->categorieArticle = $categorieArticle; return $this; }

    public function getTags(): Collection { return $this->tags; }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
        return $this;
    }

    public function removeTag(Tag $tag): static { $this->tags->removeElement($tag); return $this; }

    public function getCommentaires(): Collection { return $this->commentaires; }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setArticle($this);
        }
        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // orphanRemoval: true supprimera le commentaire en base lors du flush
            if ($commentaire->getArticle() === $this) {
                $commentaire->setArticle(null);
            }
        }
        return $this;
    }

    public function getAuteur(): ?User { return $this->auteur; }

    public function setAuteur(?User $auteur): static { $this->auteur = $auteur; return $this; }

    public function getSlug(): ?string { return $this->slug; }

    public function setSlug(?string $slug): static { $this->slug = $slug; return $this; }

    /**
     * Avant l'insertion, génère le slug si aucun n'est défini.
     */
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->generateSlug();
    }

    /**
     * Avant chaque mise à jour, régénère le slug si nécessaire.
     */
    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->generateSlug();
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
