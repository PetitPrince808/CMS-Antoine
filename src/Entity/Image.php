<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une image appartenant à une galerie.
 *
 * L'url est obligatoire : chemin relatif depuis /public,
 * ex: "uploads/galeries/photo.jpg".
 */
#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Chemin relatif depuis /public, ex: "uploads/galeries/photo.jpg" */
    #[ORM\Column(length: 500)]
    private string $url = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legende = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $addedAt = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Galerie $galerie = null;

    public function __construct()
    {
        $this->addedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getUrl(): string { return $this->url; }
    public function setUrl(string $url): static { $this->url = $url; return $this; }

    public function getLegende(): ?string { return $this->legende; }
    public function setLegende(?string $legende): static { $this->legende = $legende; return $this; }

    public function getAddedAt(): ?\DateTimeInterface { return $this->addedAt; }
    public function setAddedAt(\DateTimeInterface $addedAt): static { $this->addedAt = $addedAt; return $this; }

    public function getGalerie(): ?Galerie { return $this->galerie; }
    public function setGalerie(?Galerie $galerie): static { $this->galerie = $galerie; return $this; }
}
