<?php

namespace App\Entity;

use App\Repository\CategorieGalerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une catégorie de galerie photo.
 *
 * Exemples : "Événements", "Bureaux", "Produits", etc.
 */
#[ORM\Entity(repositoryClass: CategorieGalerieRepository::class)]
class CategorieGalerie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    /** @var Collection<int, Galerie> */
    #[ORM\OneToMany(targetEntity: Galerie::class, mappedBy: 'categorie')]
    private Collection $galeries;

    public function __construct()
    {
        $this->galeries = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }

    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    /** @return Collection<int, Galerie> */
    public function getGaleries(): Collection { return $this->galeries; }

    /**
     * Ajoute une galerie à la collection et maintient la cohérence
     * de la relation bidirectionnelle (la galerie pointe vers cette catégorie).
     */
    public function addGalerie(Galerie $galerie): static
    {
        if (!$this->galeries->contains($galerie)) {
            $this->galeries->add($galerie);
            $galerie->setCategorie($this);
        }
        return $this;
    }

    /**
     * Retire une galerie de la collection et réinitialise sa catégorie
     * si elle pointait encore vers cette catégorie.
     */
    public function removeGalerie(Galerie $galerie): static
    {
        if ($this->galeries->removeElement($galerie)) {
            if ($galerie->getCategorie() === $this) {
                $galerie->setCategorie(null);
            }
        }
        return $this;
    }

    /**
     * Rendu lisible dans les listes/menus EasyAdmin (AssociationField).
     */
    public function __toString(): string
    {
        return $this->nom ?? '';
    }
}
