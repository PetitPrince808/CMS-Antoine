<?php

namespace App\Entity;

use App\Repository\GalerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une galerie d'images.
 *
 * Une galerie regroupe plusieurs images et peut être associée à une Page.
 */
#[ORM\Entity(repositoryClass: GalerieRepository::class)]
class Galerie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $description = null;

    /** @var Collection<int, Image> */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'galerie', orphanRemoval: true)]
    private Collection $images;

    #[ORM\ManyToOne(inversedBy: 'galeries')]
    private ?CategorieGalerie $categorie = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    /** @return Collection<int, Image> */
    public function getImages(): Collection { return $this->images; }

    /**
     * Ajoute une image à la galerie et maintient la relation bidirectionnelle.
     */
    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setGalerie($this);
        }
        return $this;
    }

    /**
     * Retire une image de la galerie et détache la référence inverse si besoin.
     */
    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image) && $image->getGalerie() === $this) {
            $image->setGalerie(null);
        }
        return $this;
    }

    public function getCategorie(): ?CategorieGalerie { return $this->categorie; }
    public function setCategorie(?CategorieGalerie $categorie): static { $this->categorie = $categorie; return $this; }
}
