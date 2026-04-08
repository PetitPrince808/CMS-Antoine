# Module Pages — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ajouter la génération automatique de slug, le layout Bootstrap 5, et le contrôleur front pour afficher les pages publiquement.

**Architecture:** Lifecycle callbacks Doctrine dans `Page` pour le slug auto via `AsciiSlugger`. Un service `MenuBuilder` injecté en global Twig pour la navigation. Un `PageController` avec deux routes (`/pages` et `/pages/{slug}`) qui n'expose que les pages publiées.

**Tech Stack:** Symfony 7.4, Doctrine ORM, symfony/string (AsciiSlugger), Twig, Bootstrap 5 CDN, PHPUnit 13

---

## Fichiers concernés

| Action | Fichier | Responsabilité |
|--------|---------|----------------|
| Modifier | `src/Entity/Page.php` | Ajout lifecycle callbacks + import AsciiSlugger |
| Modifier | `src/Repository/PageRepository.php` | Ajout `findPublishedRoots()` et `findOnePublishedBySlug()` |
| Créer | `src/Service/MenuBuilder.php` | Récupère les pages racines publiées pour la nav |
| Modifier | `config/packages/twig.yaml` | Injection `menu_pages` comme global Twig |
| Créer | `src/Controller/PageController.php` | Routes `/pages` et `/pages/{slug}` |
| Modifier | `templates/base.html.twig` | Layout Bootstrap 5 avec navbar et footer |
| Créer | `templates/page/index.html.twig` | Liste des pages publiées |
| Créer | `templates/page/show.html.twig` | Affichage d'une page avec fil d'ariane |
| Créer | `tests/Entity/PageSlugTest.php` | Tests unitaires du slug auto |
| Créer | `tests/Controller/PageControllerTest.php` | Tests fonctionnels des routes front |

---

### Task 1 : Slug auto dans l'entité Page

**Files:**
- Modify: `src/Entity/Page.php`
- Test: `tests/Entity/PageSlugTest.php`

- [ ] **Step 1 : Écrire le test qui échoue**

Créer `tests/Entity/PageSlugTest.php` :

```php
<?php

namespace App\Tests\Entity;

use App\Entity\Page;
use PHPUnit\Framework\TestCase;

class PageSlugTest extends TestCase
{
    public function testSlugGenereDepuisTitre(): void
    {
        $page = new Page();
        $page->setTitre('À propos de nous');
        $page->generateSlug();

        $this->assertSame('a-propos-de-nous', $page->getSlug());
    }

    public function testSlugManuelNonEcrase(): void
    {
        $page = new Page();
        $page->setTitre('À propos de nous');
        $page->setSlug('mon-slug-perso');
        $page->generateSlug();

        // Si un slug est déjà défini, il ne doit pas être remplacé
        $this->assertSame('mon-slug-perso', $page->getSlug());
    }

    public function testCaracteresSpeciauxNormalises(): void
    {
        $page = new Page();
        $page->setTitre('Ça & Là : "test"');
        $page->generateSlug();

        $this->assertSame('ca-la-test', $page->getSlug());
    }
}
```

- [ ] **Step 2 : Lancer le test pour vérifier qu'il échoue**

```bash
php bin/phpunit tests/Entity/PageSlugTest.php
```

Attendu : FAIL — `Call to undefined method App\Entity\Page::generateSlug()`

- [ ] **Step 3 : Implémenter le slug dans `src/Entity/Page.php`**

Ajouter en haut du fichier les imports suivants (après `use Doctrine\ORM\Mapping as ORM;`) :

```php
use Symfony\Component\String\Slugger\AsciiSlugger;
```

Ajouter les deux attributs lifecycle après `#[ORM\Entity(repositoryClass: PageRepository::class)]` :

```php
#[ORM\HasLifecycleCallbacks]
```

Ajouter les deux méthodes suivantes dans la classe, avant la fermeture `}` :

```php
/**
 * Appelé automatiquement par Doctrine avant une insertion ou une mise à jour.
 * Génère le slug depuis le titre uniquement si aucun slug n'a été saisi manuellement.
 */
#[ORM\PrePersist]
#[ORM\PreUpdate]
public function generateSlug(): void
{
    if (!empty($this->slug)) {
        return;
    }

    $slugger = new AsciiSlugger('fr');
    $this->slug = strtolower($slugger->slug($this->titre ?? ''));
}
```

- [ ] **Step 4 : Lancer le test pour vérifier qu'il passe**

```bash
php bin/phpunit tests/Entity/PageSlugTest.php
```

Attendu : `OK (3 tests, 3 assertions)`

- [ ] **Step 5 : Commit**

```bash
git add src/Entity/Page.php tests/Entity/PageSlugTest.php
git commit -m "[Pages] Slug auto-généré depuis le titre via AsciiSlugger"
```

---

### Task 2 : Méthodes de requête dans PageRepository

**Files:**
- Modify: `src/Repository/PageRepository.php`

- [ ] **Step 1 : Remplacer le contenu de `src/Repository/PageRepository.php`**

```php
<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Page.
 *
 * @extends ServiceEntityRepository<Page>
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    /**
     * Retourne toutes les pages racines publiées (sans parent),
     * triées par titre pour un menu stable.
     *
     * @return Page[]
     */
    public function findPublishedRoots(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.statut = :statut')
            ->andWhere('p.parent IS NULL')
            ->setParameter('statut', 'publie')
            ->orderBy('p.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve une page publiée par son slug.
     * Retourne null si le slug n'existe pas ou si la page n'est pas publiée.
     */
    public function findOnePublishedBySlug(string $slug): ?Page
    {
        return $this->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->andWhere('p.statut = :statut')
            ->setParameter('slug', $slug)
            ->setParameter('statut', 'publie')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
```

- [ ] **Step 2 : Vérifier que le conteneur Symfony compile**

```bash
php bin/console cache:clear
```

Attendu : `[OK] Cache for the "dev" environment (debug=true) was successfully cleared.`

- [ ] **Step 3 : Commit**

```bash
git add src/Repository/PageRepository.php
git commit -m "[Pages] Ajout findPublishedRoots et findOnePublishedBySlug dans PageRepository"
```

---

### Task 3 : Service MenuBuilder

**Files:**
- Create: `src/Service/MenuBuilder.php`
- Modify: `config/packages/twig.yaml`

- [ ] **Step 1 : Créer `src/Service/MenuBuilder.php`**

```php
<?php

namespace App\Service;

use App\Repository\PageRepository;

/**
 * Fournit les données de navigation communes à tous les templates.
 *
 * Centralisé ici pour éviter de répéter la requête dans chaque contrôleur.
 */
class MenuBuilder
{
    public function __construct(private readonly PageRepository $pageRepository)
    {
    }

    /**
     * Retourne les pages racines publiées pour construire le menu principal.
     *
     * @return \App\Entity\Page[]
     */
    public function getMenuPages(): array
    {
        return $this->pageRepository->findPublishedRoots();
    }
}
```

- [ ] **Step 2 : Modifier `config/packages/twig.yaml` pour injecter le menu**

Remplacer le contenu par :

```yaml
twig:
    file_name_pattern: '*.twig'
    globals:
        menu_pages: '@App\Service\MenuBuilder'

when@test:
    twig:
        strict_variables: true
```

- [ ] **Step 3 : Vérifier que le conteneur compile**

```bash
php bin/console cache:clear
```

Attendu : `[OK] Cache for the "dev" environment (debug=true) was successfully cleared.`

- [ ] **Step 4 : Commit**

```bash
git add src/Service/MenuBuilder.php config/packages/twig.yaml
git commit -m "[Pages] Service MenuBuilder injecté comme global Twig"
```

---

### Task 4 : Layout Bootstrap 5

**Files:**
- Modify: `templates/base.html.twig`

- [ ] **Step 1 : Remplacer `templates/base.html.twig`**

```twig
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{% block title %}CMS DISII{% endblock %}</title>

    {# Bootstrap 5 via CDN — pas de build step nécessaire #}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">

    {% block stylesheets %}{% endblock %}
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ path('app_home') }}">CMS DISII</a>
            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    {# menu_pages est fourni par MenuBuilder injecté dans twig.yaml #}
                    {% for page in menu_pages.menuPages %}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ path('app_page_show', {slug: page.slug}) }}">
                                {{ page.titre }}
                            </a>
                        </li>
                    {% endfor %}
                </ul>
                <ul class="navbar-nav ms-auto">
                    {% if app.user %}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ path('admin') }}">Admin</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ path('app_logout') }}">Déconnexion</a>
                        </li>
                    {% else %}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ path('app_login') }}">Connexion</a>
                        </li>
                    {% endif %}
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        {% block body %}{% endblock %}
    </main>

    <footer class="bg-dark text-light py-3 mt-5">
        <div class="container text-center">
            <small>CMS DISII &copy; {{ "now"|date("Y") }}</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmO0fHBW2I8TZzXFqQHWzpktNtYl"
            crossorigin="anonymous"></script>
    {% block javascripts %}{% endblock %}
</body>
</html>
```

- [ ] **Step 2 : Vérifier que Twig parse le template sans erreur**

```bash
php bin/console lint:twig templates/base.html.twig
```

Attendu : `[OK] All 1 Twig files contain valid syntax.`

- [ ] **Step 3 : Commit**

```bash
git add templates/base.html.twig
git commit -m "[Pages] Layout Bootstrap 5 avec navbar dynamique et footer"
```

---

### Task 5 : PageController et templates front

**Files:**
- Create: `src/Controller/PageController.php`
- Create: `templates/page/index.html.twig`
- Create: `templates/page/show.html.twig`
- Test: `tests/Controller/PageControllerTest.php`

- [ ] **Step 1 : Écrire le test fonctionnel qui échoue**

Créer `tests/Controller/PageControllerTest.php` :

```php
<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    public function testIndexRetourne200(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pages');

        $this->assertResponseIsSuccessful();
    }

    public function testSlugInexistantRetourne404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pages/slug-qui-nexiste-pas');

        $this->assertResponseStatusCodeSame(404);
    }
}
```

- [ ] **Step 2 : Lancer le test pour vérifier qu'il échoue**

```bash
php bin/phpunit tests/Controller/PageControllerTest.php
```

Attendu : FAIL — `No route found for "GET /pages"`

- [ ] **Step 3 : Créer `src/Controller/PageController.php`**

```php
<?php

namespace App\Controller;

use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    /**
     * Liste toutes les pages racines publiées.
     * Point d'entrée public du site.
     */
    #[Route('/pages', name: 'app_page_index')]
    public function index(PageRepository $pageRepository): Response
    {
        $pages = $pageRepository->findPublishedRoots();

        return $this->render('page/index.html.twig', [
            'pages' => $pages,
        ]);
    }

    /**
     * Affiche une page par son slug.
     * Retourne 404 si la page n'existe pas ou n'est pas publiée.
     */
    #[Route('/pages/{slug}', name: 'app_page_show')]
    public function show(string $slug, PageRepository $pageRepository): Response
    {
        $page = $pageRepository->findOnePublishedBySlug($slug);

        if (!$page) {
            throw $this->createNotFoundException("Page introuvable : $slug");
        }

        return $this->render('page/show.html.twig', [
            'page' => $page,
        ]);
    }
}
```

- [ ] **Step 4 : Ajouter une route d'accueil dans `src/Controller/PageController.php`**

Ajouter cette action avant `index()` dans la même classe :

```php
/**
 * Page d'accueil — redirige vers la liste des pages.
 */
#[Route('/', name: 'app_home')]
public function home(PageRepository $pageRepository): Response
{
    $pages = $pageRepository->findPublishedRoots();

    return $this->render('page/index.html.twig', [
        'pages' => $pages,
    ]);
}
```

- [ ] **Step 5 : Créer `templates/page/index.html.twig`**

```twig
{% extends 'base.html.twig' %}

{% block title %}Pages — CMS DISII{% endblock %}

{% block body %}
    <h1 class="mb-4">Pages</h1>

    {% if pages is empty %}
        <p class="text-muted">Aucune page publiée pour le moment.</p>
    {% else %}
        <div class="list-group">
            {% for page in pages %}
                <a href="{{ path('app_page_show', {slug: page.slug}) }}"
                   class="list-group-item list-group-item-action">
                    {{ page.titre }}
                    {% if page.categoriePage %}
                        <span class="badge bg-secondary ms-2">{{ page.categoriePage.nom }}</span>
                    {% endif %}
                </a>
            {% endfor %}
        </div>
    {% endif %}
{% endblock %}
```

- [ ] **Step 6 : Créer `templates/page/show.html.twig`**

```twig
{% extends 'base.html.twig' %}

{% block title %}{{ page.titre }} — CMS DISII{% endblock %}

{% block body %}
    {# Fil d'ariane : affiche le parent si la page en a un #}
    {% if page.parent %}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ path('app_page_show', {slug: page.parent.slug}) }}">
                        {{ page.parent.titre }}
                    </a>
                </li>
                <li class="breadcrumb-item active">{{ page.titre }}</li>
            </ol>
        </nav>
    {% endif %}

    <article>
        <h1 class="mb-4">{{ page.titre }}</h1>

        <div class="content">
            {{ page.paragraphes|raw }}
        </div>

        {# Galerie associée si présente #}
        {% if page.galerie and page.galerie.images|length > 0 %}
            <hr>
            <h2 class="h4 mt-4">{{ page.galerie.nom }}</h2>
            <div class="row row-cols-2 row-cols-md-3 g-3 mt-2">
                {% for image in page.galerie.images %}
                    <div class="col">
                        <figure class="figure">
                            <img src="{{ asset(image.url) }}"
                                 class="figure-img img-fluid rounded"
                                 alt="{{ image.legende ?? page.galerie.nom }}">
                            {% if image.legende %}
                                <figcaption class="figure-caption">{{ image.legende }}</figcaption>
                            {% endif %}
                        </figure>
                    </div>
                {% endfor %}
            </div>
        {% endif %}

        {# Pages enfants si elles existent #}
        {% set enfants_publies = page.children|filter(c => c.statut == 'publie') %}
        {% if enfants_publies|length > 0 %}
            <hr>
            <h2 class="h4 mt-4">Sous-pages</h2>
            <ul class="list-group mt-2">
                {% for enfant in enfants_publies %}
                    <li class="list-group-item">
                        <a href="{{ path('app_page_show', {slug: enfant.slug}) }}">
                            {{ enfant.titre }}
                        </a>
                    </li>
                {% endfor %}
            </ul>
        {% endif %}
    </article>
{% endblock %}
```

- [ ] **Step 7 : Vérifier les templates Twig**

```bash
php bin/console lint:twig templates/page/
```

Attendu : `[OK] All 2 Twig files contain valid syntax.`

- [ ] **Step 8 : Lancer les tests fonctionnels**

```bash
php bin/phpunit tests/Controller/PageControllerTest.php
```

Attendu : `OK (2 tests, 2 assertions)`

- [ ] **Step 9 : Lancer toute la suite de tests**

```bash
php bin/phpunit
```

Attendu : `OK (35 tests, ...)`

- [ ] **Step 10 : Commit**

```bash
git add src/Controller/PageController.php templates/page/ tests/Controller/PageControllerTest.php
git commit -m "[Pages] Contrôleur front + templates index et show Bootstrap 5"
```

---

### Task 6 : Mettre à jour le TODO.md

**Files:**
- Modify: `TODO.md`

- [ ] **Step 1 : Mettre à jour `TODO.md`**

Déplacer les items suivants de `❌ À faire` vers `✅ Fait` :

```markdown
### Pages
- [x] Slug auto-généré (AsciiSlugger, lifecycle callbacks)
- [x] Éditeur riche dans l'admin (TextEditorField EasyAdmin 5)
- [x] Contrôleur front + templates Bootstrap 5
- [x] Arborescence parent/enfant dans le front (fil d'ariane + sous-pages)
```

- [ ] **Step 2 : Commit**

```bash
git add TODO.md
git commit -m "[Projet] TODO — module Pages terminé"
```
