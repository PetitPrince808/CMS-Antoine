# Fondations — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Mettre en place le projet Symfony complet avec authentification par rôles, toutes les entités Doctrine et les migrations, prêt pour développer les modules métier.

**Architecture:** Symfony 7 skeleton + EasyAdmin 4 + Doctrine ORM. L'authentification utilise le système de sécurité natif Symfony (form_login) avec l'entité User. Toutes les entités sont créées dès le départ pour que les migrations soient cohérentes et ne nécessitent pas de retouches ultérieures.

**Tech Stack:** Symfony 7, EasyAdmin 4, Doctrine ORM + Migrations, MySQL (MAMP), PHPUnit via symfony/test-pack, Symfony Security.

---

## Structure des fichiers

```
.
├── src/
│   ├── Entity/
│   │   ├── User.php
│   │   ├── Article.php
│   │   ├── CategorieArticle.php
│   │   ├── Tag.php
│   │   ├── Commentaire.php
│   │   ├── Page.php
│   │   ├── CategoriePage.php
│   │   ├── Galerie.php
│   │   └── Image.php
│   ├── Repository/          ← généré automatiquement par Doctrine
│   └── Controller/
│       └── SecurityController.php
├── config/
│   └── packages/
│       └── security.yaml
├── migrations/              ← généré par doctrine:migrations:diff
├── templates/
│   └── security/
│       └── login.html.twig
├── tests/
│   └── Entity/
│       ├── UserTest.php
│       ├── ArticleTest.php
│       └── PageTest.php
├── .env
└── .env.test
```

---

## Task 1 : Créer le projet Symfony

**Files:**
- Create : `composer.json`, toute la structure Symfony skeleton

- [ ] **Étape 1 : Initialiser le projet Symfony dans le dossier courant**

```bash
cd /Applications/MAMP/htdocs/Symfony/Projet-2
composer create-project symfony/skeleton:"7.*" tmp_symfony
cp -r tmp_symfony/. .
rm -rf tmp_symfony
```

> Le dossier contient déjà des fichiers (CLAUDE.md, etc.) donc on passe par un dossier temporaire.

- [ ] **Étape 2 : Installer les dépendances essentielles**

```bash
composer require twig symfony/asset symfony/form symfony/validator
composer require doctrine/orm doctrine/doctrine-bundle doctrine/doctrine-migrations-bundle
composer require symfony/security-bundle
composer require easycorp/easyadmin-bundle
composer require --dev symfony/test-pack symfony/browser-kit
```

- [ ] **Étape 3 : Vérifier que Symfony démarre**

```bash
php bin/console about
```

Résultat attendu : tableau récapitulatif Symfony sans erreur.

- [ ] **Étape 4 : Commit**

```bash
git init
git add .
git commit -m "[Setup] Initialisation du projet Symfony 7 avec EasyAdmin et Doctrine"
git remote add origin <url-du-repo-cms-disii-Antoine>
git push -u origin main
```

---

## Task 2 : Configurer la base de données

**Files:**
- Modify : `.env`
- Create : `.env.test`

- [ ] **Étape 1 : Configurer `.env` pour MySQL MAMP**

Remplacer la ligne `DATABASE_URL` dans `.env` par :

```dotenv
DATABASE_URL="mysql://root:root@127.0.0.1:8889/cms_disii?serverVersion=8.0&charset=utf8mb4"
```

> Port 8889 = port MySQL par défaut de MAMP. Adapter si différent. Mot de passe MAMP par défaut = `root`.

- [ ] **Étape 2 : Créer `.env.test` pour la base de test PHPUnit**

```dotenv
DATABASE_URL="mysql://root:root@127.0.0.1:8889/cms_disii_test?serverVersion=8.0&charset=utf8mb4"
```

- [ ] **Étape 3 : Créer les bases de données**

```bash
php bin/console doctrine:database:create
php bin/console doctrine:database:create --env=test
```

Résultat attendu : `Created database "cms_disii"` et `Created database "cms_disii_test"`.

- [ ] **Étape 4 : Commit**

```bash
git add .env .env.test
git commit -m "[Setup] Configuration MySQL MAMP + base de test"
git push
```

---

## Task 3 : Entité User + Authentification

**Files:**
- Create : `src/Entity/User.php`
- Create : `src/Repository/UserRepository.php`
- Modify : `config/packages/security.yaml`
- Create : `src/Controller/SecurityController.php`
- Create : `templates/security/login.html.twig`
- Create : `tests/Entity/UserTest.php`

- [ ] **Étape 1 : Écrire le test en premier**

```php
// tests/Entity/UserTest.php
namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_REDACTEUR']);

        // ROLE_USER est toujours ajouté automatiquement par Symfony
        $this->assertContains('ROLE_REDACTEUR', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $this->assertSame('test@example.com', $user->getUserIdentifier());
    }

    public function testNom(): void
    {
        $user = new User();
        $user->setNom('Antoine');

        $this->assertSame('Antoine', $user->getNom());
    }
}
```

- [ ] **Étape 2 : Lancer le test — vérifier qu'il échoue**

```bash
php bin/phpunit tests/Entity/UserTest.php
```

Résultat attendu : FAIL — `Class "App\Entity\User" not found`.

- [ ] **Étape 3 : Créer l'entité User**

```php
// src/Entity/User.php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    // Rôles stockés en JSON : ['ROLE_ADMIN'], ['ROLE_REDACTEUR'], ou [] pour ROLE_USER
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // Symfony exige que tout utilisateur ait au moins ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function eraseCredentials(): void {}
}
```

- [ ] **Étape 4 : Créer le Repository User**

```php
// src/Repository/UserRepository.php
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }
        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
```

- [ ] **Étape 5 : Configurer la sécurité**

Remplacer le contenu de `config/packages/security.yaml` par :

```yaml
security:
    password_hashers:
        App\Entity\User:
            algorithm: auto

    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false

        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: app_login
                check_path: app_login
                enable_csrf: true
            logout:
                path: app_logout
                target: app_login

    access_control:
        - { path: ^/admin, roles: ROLE_REDACTEUR }
        - { path: ^/login, roles: PUBLIC_ACCESS }
```

- [ ] **Étape 6 : Créer le SecurityController**

```php
// src/Controller/SecurityController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony intercepte cette route automatiquement — le corps est ignoré
        throw new \LogicException('Cette méthode ne doit jamais être appelée.');
    }
}
```

- [ ] **Étape 7 : Créer le template de login**

```twig
{# templates/security/login.html.twig #}
{% extends 'base.html.twig' %}

{% block title %}Connexion{% endblock %}

{% block body %}
<form action="{{ path('app_login') }}" method="post">
    {% if error %}
        <div class="alert alert-danger">{{ error.messageKey|trans(error.messageData, 'security') }}</div>
    {% endif %}

    <input type="hidden" name="_csrf_token" value="{{ csrf_token('authenticate') }}">

    <div>
        <label for="inputEmail">Email</label>
        <input type="email" id="inputEmail" name="_username" value="{{ last_username }}" required autofocus>
    </div>

    <div>
        <label for="inputPassword">Mot de passe</label>
        <input type="password" id="inputPassword" name="_password" required>
    </div>

    <button type="submit">Connexion</button>
</form>
{% endblock %}
```

- [ ] **Étape 8 : Lancer le test — vérifier qu'il passe**

```bash
php bin/phpunit tests/Entity/UserTest.php
```

Résultat attendu : OK (3 tests, 4 assertions).

- [ ] **Étape 9 : Commit**

```bash
git add src/Entity/User.php src/Repository/UserRepository.php config/packages/security.yaml src/Controller/SecurityController.php templates/security/ tests/Entity/UserTest.php
git commit -m "[Auth] Entité User, sécurité Symfony et formulaire de connexion"
git push
```

---

## Task 4 : Entités Blog (CategorieArticle, Tag, Article, Commentaire)

**Files:**
- Create : `src/Entity/CategorieArticle.php`
- Create : `src/Entity/Tag.php`
- Create : `src/Entity/Article.php`
- Create : `src/Entity/Commentaire.php`
- Create : `tests/Entity/ArticleTest.php`

- [ ] **Étape 1 : Écrire le test**

```php
// tests/Entity/ArticleTest.php
namespace App\Tests\Entity;

use App\Entity\Article;
use App\Entity\CategorieArticle;
use App\Entity\Tag;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    public function testStatutParDefaut(): void
    {
        $article = new Article();
        // Un article créé sans statut doit être en brouillon par défaut
        $this->assertSame('brouillon', $article->getStatut());
    }

    public function testAjoutTag(): void
    {
        $article = new Article();
        $tag = new Tag();
        $tag->setNom('Symfony');

        $article->addTag($tag);

        $this->assertCount(1, $article->getTags());
        $this->assertTrue($article->getTags()->contains($tag));
    }

    public function testCategorie(): void
    {
        $article = new Article();
        $categorie = new CategorieArticle();
        $categorie->setNom('Tutoriels');

        $article->setCategorieArticle($categorie);

        $this->assertSame('Tutoriels', $article->getCategorieArticle()->getNom());
    }

    public function testAuteur(): void
    {
        $article = new Article();
        $user = new User();
        $user->setNom('Antoine');

        $article->setAuteur($user);

        $this->assertSame('Antoine', $article->getAuteur()->getNom());
    }
}
```

- [ ] **Étape 2 : Lancer le test — vérifier qu'il échoue**

```bash
php bin/phpunit tests/Entity/ArticleTest.php
```

Résultat attendu : FAIL — classes non trouvées.

- [ ] **Étape 3 : Créer l'entité CategorieArticle**

```php
// src/Entity/CategorieArticle.php
namespace App\Entity;

use App\Repository\CategorieArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieArticleRepository::class)]
class CategorieArticle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'categorieArticle')]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getArticles(): Collection { return $this->articles; }
}
```

- [ ] **Étape 4 : Créer l'entité Tag**

```php
// src/Entity/Tag.php
namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $nom = null;

    #[ORM\ManyToMany(targetEntity: Article::class, mappedBy: 'tags')]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getArticles(): Collection { return $this->articles; }
}
```

- [ ] **Étape 5 : Créer l'entité Article**

```php
// src/Entity/Article.php
namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
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

    // Valeurs possibles : 'brouillon', 'publie', 'archive'
    #[ORM\Column(length: 20)]
    private string $statut = 'brouillon';

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

    public function getAuteur(): ?User { return $this->auteur; }
    public function setAuteur(?User $auteur): static { $this->auteur = $auteur; return $this; }
}
```

- [ ] **Étape 6 : Créer l'entité Commentaire**

```php
// src/Entity/Commentaire.php
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
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    // Valeurs possibles : 'en_attente', 'approuve', 'rejete'
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

    public function getContenu(): ?string { return $this->contenu; }
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
```

- [ ] **Étape 7 : Lancer le test — vérifier qu'il passe**

```bash
php bin/phpunit tests/Entity/ArticleTest.php
```

Résultat attendu : OK (4 tests, 5 assertions).

- [ ] **Étape 8 : Commit**

```bash
git add src/Entity/CategorieArticle.php src/Entity/Tag.php src/Entity/Article.php src/Entity/Commentaire.php tests/Entity/ArticleTest.php
git commit -m "[Blog] Entités CategorieArticle, Tag, Article et Commentaire"
git push
```

---

## Task 5 : Entités Pages (CategoriePage, Page)

**Files:**
- Create : `src/Entity/CategoriePage.php`
- Create : `src/Entity/Page.php`
- Create : `tests/Entity/PageTest.php`

- [ ] **Étape 1 : Écrire le test**

```php
// tests/Entity/PageTest.php
namespace App\Tests\Entity;

use App\Entity\Page;
use App\Entity\CategoriePage;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    public function testStatutParDefaut(): void
    {
        $page = new Page();
        $this->assertSame('brouillon', $page->getStatut());
    }

    public function testDatesInitialisees(): void
    {
        $page = new Page();
        // createdAt et updatedAt doivent être définis à la création
        $this->assertInstanceOf(\DateTimeInterface::class, $page->getCreatedAt());
        $this->assertInstanceOf(\DateTimeInterface::class, $page->getUpdatedAt());
    }

    public function testRelationParent(): void
    {
        $parent = new Page();
        $parent->setTitre('Page parente');

        $enfant = new Page();
        $enfant->setParent($parent);

        $this->assertSame($parent, $enfant->getParent());
    }

    public function testCategorie(): void
    {
        $page = new Page();
        $categorie = new CategoriePage();
        $categorie->setNom('Institutionnel');

        $page->setCategoriePage($categorie);

        $this->assertSame('Institutionnel', $page->getCategoriePage()->getNom());
    }
}
```

- [ ] **Étape 2 : Lancer le test — vérifier qu'il échoue**

```bash
php bin/phpunit tests/Entity/PageTest.php
```

Résultat attendu : FAIL.

- [ ] **Étape 3 : Créer l'entité CategoriePage**

```php
// src/Entity/CategoriePage.php
namespace App\Entity;

use App\Repository\CategoriePageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriePageRepository::class)]
class CategoriePage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\OneToMany(targetEntity: Page::class, mappedBy: 'categoriePage')]
    private Collection $pages;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getPages(): Collection { return $this->pages; }
}
```

- [ ] **Étape 4 : Créer l'entité Page**

```php
// src/Entity/Page.php
namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageRepository::class)]
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

    // Slug = URL conviviale ex: "a-propos" — doit être unique
    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $metaDescription = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    // Valeurs possibles : 'brouillon', 'publie', 'archive'
    #[ORM\Column(length: 20)]
    private string $statut = 'brouillon';

    // Relation récursive : une page peut avoir une page parente
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

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

    public function getChildren(): Collection { return $this->children; }

    public function getGalerie(): ?Galerie { return $this->galerie; }
    public function setGalerie(?Galerie $galerie): static { $this->galerie = $galerie; return $this; }

    public function getCategoriePage(): ?CategoriePage { return $this->categoriePage; }
    public function setCategoriePage(?CategoriePage $categoriePage): static { $this->categoriePage = $categoriePage; return $this; }
}
```

- [ ] **Étape 5 : Lancer le test — vérifier qu'il passe**

```bash
php bin/phpunit tests/Entity/PageTest.php
```

Résultat attendu : OK (4 tests, 5 assertions).

- [ ] **Étape 6 : Commit**

```bash
git add src/Entity/CategoriePage.php src/Entity/Page.php tests/Entity/PageTest.php
git commit -m "[Pages] Entités CategoriePage et Page avec relation parent/enfant"
git push
```

---

## Task 6 : Entités Médias (Galerie, Image)

**Files:**
- Create : `src/Entity/Galerie.php`
- Create : `src/Entity/Image.php`

- [ ] **Étape 1 : Créer l'entité Galerie**

```php
// src/Entity/Galerie.php
namespace App\Entity;

use App\Repository\GalerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'galerie', orphanRemoval: true)]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getImages(): Collection { return $this->images; }
    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setGalerie($this);
        }
        return $this;
    }
    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image) && $image->getGalerie() === $this) {
            $image->setGalerie(null);
        }
        return $this;
    }
}
```

- [ ] **Étape 2 : Créer l'entité Image**

```php
// src/Entity/Image.php
namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Chemin relatif depuis /public ex: "uploads/galeries/photo.jpg"
    #[ORM\Column(length: 500)]
    private ?string $url = null;

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

    public function getUrl(): ?string { return $this->url; }
    public function setUrl(string $url): static { $this->url = $url; return $this; }

    public function getLegende(): ?string { return $this->legende; }
    public function setLegende(?string $legende): static { $this->legende = $legende; return $this; }

    public function getAddedAt(): ?\DateTimeInterface { return $this->addedAt; }
    public function setAddedAt(\DateTimeInterface $addedAt): static { $this->addedAt = $addedAt; return $this; }

    public function getGalerie(): ?Galerie { return $this->galerie; }
    public function setGalerie(?Galerie $galerie): static { $this->galerie = $galerie; return $this; }
}
```

- [ ] **Étape 3 : Lancer tous les tests pour vérifier rien n'est cassé**

```bash
php bin/phpunit
```

Résultat attendu : OK (tous les tests passent).

- [ ] **Étape 4 : Commit**

```bash
git add src/Entity/Galerie.php src/Entity/Image.php
git commit -m "[Médias] Entités Galerie et Image"
git push
```

---

## Task 7 : Génération des migrations et vérification du schéma

**Files:**
- Create : `migrations/VersionXXXXXXXXXXXX.php` (généré automatiquement)

- [ ] **Étape 1 : Générer la migration**

```bash
php bin/console doctrine:migrations:diff
```

Résultat attendu : fichier `migrations/VersionXXX.php` créé avec les CREATE TABLE pour toutes les entités.

- [ ] **Étape 2 : Lire la migration générée et vérifier**

Ouvrir le fichier généré dans `migrations/`. Vérifier que les tables suivantes apparaissent dans le SQL :
- `user`, `article`, `categorie_article`, `tag`, `article_tag` (table pivot), `commentaire`
- `page`, `categorie_page`, `galerie`, `image`

- [ ] **Étape 3 : Exécuter la migration en base principale**

```bash
php bin/console doctrine:migrations:migrate
```

Répondre `yes` à la confirmation. Résultat attendu : migration exécutée sans erreur.

- [ ] **Étape 4 : Exécuter la migration en base de test**

```bash
php bin/console doctrine:migrations:migrate --env=test
```

- [ ] **Étape 5 : Vérifier le schéma**

```bash
php bin/console doctrine:schema:validate
```

Résultat attendu : `[OK] The mapping files are correct.` et `[OK] The database schema is in sync with the mapping files.`

- [ ] **Étape 6 : Commit**

```bash
git add migrations/
git commit -m "[BDD] Migration initiale — création de toutes les tables"
git push
```

---

## Task 8 : Créer un utilisateur admin en base

**Files:**
- Create : `src/Command/CreateAdminCommand.php`

- [ ] **Étape 1 : Créer la commande Symfony**

```php
// src/Command/CreateAdminCommand.php
namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// Commande pour créer rapidement un admin en dev — ne pas exposer en prod
#[AsCommand(name: 'app:create-admin', description: 'Crée un utilisateur administrateur')]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setEmail('admin@cms-disii.local');
        $user->setNom('Administrateur');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->hasher->hashPassword($user, 'admin1234'));

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('Admin créé : admin@cms-disii.local / admin1234');

        return Command::SUCCESS;
    }
}
```

- [ ] **Étape 2 : Exécuter la commande**

```bash
php bin/console app:create-admin
```

Résultat attendu : `Admin créé : admin@cms-disii.local / admin1234`.

- [ ] **Étape 3 : Tester la connexion**

Ouvrir [http://localhost:8888/login](http://localhost:8888/login) (port MAMP) et se connecter avec `admin@cms-disii.local` / `admin1234`.

> Si le port MAMP est différent, adapter l'URL.

- [ ] **Étape 4 : Commit**

```bash
git add src/Command/CreateAdminCommand.php
git commit -m "[Auth] Commande de création d'un admin pour le développement"
git push
```

---

## Vérification finale

- [ ] Lancer toute la suite de tests

```bash
php bin/phpunit
```

Résultat attendu : tous les tests passent (vert).

- [ ] Vérifier le schéma BDD une dernière fois

```bash
php bin/console doctrine:schema:validate
```

---

**Plan 1 terminé.** Les fondations sont en place : Symfony installé, toutes les entités créées, migrations exécutées, authentification fonctionnelle, compte admin disponible.

Prochaine étape → **Plan 2 : Contenu** (Pages CRUD + CKEditor + Blog).
