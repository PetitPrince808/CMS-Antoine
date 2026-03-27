<?php

namespace App\Entity;

use App\Repository\CategoriePageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une catégorie de page de contenu.
 *
 * Exemples : "Institutionnel", "Légal", "Aide", etc.
 */
#[ORM\Entity(repositoryClass: CategoriePageRepository::class)]
class CategoriePage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    /** @var Collection<int, Page> */
    #[ORM\OneToMany(targetEntity: Page::class, mappedBy: 'categoriePage')]
    private Collection $pages;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }

    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    /** @return Collection<int, Page> */
    public function getPages(): Collection { return $this->pages; }

    /**
     * Ajoute une page à la collection et maintient la cohérence
     * de la relation bidirectionnelle (la page pointe vers cette catégorie).
     */
    public function addPage(Page $page): static
    {
        if (!$this->pages->contains($page)) {
            $this->pages->add($page);
            $page->setCategoriePage($this);
        }
        return $this;
    }

    /**
     * Retire une page de la collection et réinitialise sa catégorie
     * si elle pointait encore vers cette catégorie.
     */
    public function removePage(Page $page): static
    {
        if ($this->pages->removeElement($page)) {
            if ($page->getCategoriePage() === $this) {
                $page->setCategoriePage(null);
            }
        }
        return $this;
    }
}
