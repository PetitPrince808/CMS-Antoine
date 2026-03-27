<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $contenu = '';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    /** Valeurs possibles : 'en_attente', 'approuve', 'rejete' */
    #[ORM\Column(length: 20)]
    private string $statut = 'en_attente';

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    #[ORM\ManyToOne]
    private ?User $auteur = null;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getContenu(): string { return $this->contenu; }

    public function setContenu(string $contenu): static { $this->contenu = $contenu; return $this; }

    public function getDate(): ?\DateTimeInterface { return $this->date; }

    public function setDate(\DateTimeInterface $date): static { $this->date = $date; return $this; }

    public function getStatut(): string { return $this->statut; }

    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getArticle(): ?Article { return $this->article; }

    public function setArticle(?Article $article): static { $this->article = $article; return $this; }

    public function getAuteur(): ?User { return $this->auteur; }

    public function setAuteur(?User $auteur): static { $this->auteur = $auteur; return $this; }
}
