# Module Pages — Design Spec

**Date :** 2026-04-07
**Scope :** Slug auto-généré, CKEditor dans l'admin, contrôleur front + templates Bootstrap 5

---

## Contexte

L'entité `Page` existe déjà avec ses champs (`titre`, `paragraphes`, `slug`, `metaDescription`, `statut`, `parent`, `categoriePage`, `galerie`). EasyAdmin est câblé avec `PageCrudController`. Il manque :

1. La génération automatique du slug
2. L'éditeur riche pour le champ `paragraphes` dans l'admin
3. Le contrôleur et les templates front pour l'affichage public

---

## Architecture

### Admin

- **Slug auto** : lifecycle callbacks Doctrine (`#[ORM\PrePersist]` et `#[ORM\PreUpdate]`) dans l'entité `Page`. Utilise `symfony/string` (`AsciiSlugger`) pour normaliser le titre. Si le slug est déjà renseigné, il est conservé tel quel (respect du slug manuel).
- **Éditeur riche** : EasyAdmin 5 fournit `TextEditorField` nativement. Pas besoin de bundle CKEditor externe.
- **PageCrudController** : déjà créé, aucun changement nécessaire — `TextEditorField` est déjà utilisé pour `paragraphes`.

### Front

- **`PageController`** : deux actions
  - `index()` → route `/pages` — liste toutes les pages racines publiées
  - `show(string $slug)` → route `/pages/{slug}` — affiche une page par son slug
- **Visibilité** : seules les pages avec `statut = 'publie'` sont accessibles au public. Slug introuvable ou statut différent → 404.
- **`MenuBuilder`** (service) : récupère les pages racines publiées pour la navigation. Injecté comme variable Twig globale dans `config/packages/twig.yaml` → disponible dans tous les templates sans répéter la query.

### Templates Twig

- `templates/base.html.twig` : layout Bootstrap 5 (CDN), navbar avec pages racines, footer minimaliste
- `templates/page/index.html.twig` : liste des pages avec titre et lien
- `templates/page/show.html.twig` : affichage d'une page (titre, contenu, fil d'ariane parent/enfant, galerie associée si présente)

---

## Data Flow

### Création / édition (admin)
1. Rédacteur soumet le formulaire EasyAdmin
2. `PrePersist` / `PreUpdate` : si `slug` est vide → génère depuis `titre` via `AsciiSlugger` (`"À propos"` → `"a-propos"`)
3. Si slug déjà renseigné → conservé sans modification
4. Doctrine flush

### Affichage public
1. Requête `GET /pages/{slug}`
2. `PageController::show()` : query `PageRepository::findOneBySlugAndStatut($slug, 'publie')`
3. Si `null` → `throw $this->createNotFoundException()`
4. Template reçoit : `page`, `page.children` (publiés uniquement), `page.galerie`

---

## Fichiers créés / modifiés

| Action | Fichier |
|--------|---------|
| Modifier | `src/Entity/Page.php` — ajout lifecycle callbacks + import AsciiSlugger |
| Créer | `src/Service/MenuBuilder.php` |
| Créer | `src/Controller/PageController.php` |
| Modifier | `src/Repository/PageRepository.php` — ajout `findOneBySlugAndStatut()` |
| Modifier | `config/packages/twig.yaml` — injection `menu_pages` en global |
| Modifier | `templates/base.html.twig` — layout Bootstrap 5 |
| Créer | `templates/page/index.html.twig` |
| Créer | `templates/page/show.html.twig` |
| Créer | `tests/Entity/PageSlugTest.php` |
| Créer | `tests/Controller/PageControllerTest.php` |

---

## Tests

### Unitaires — `PageSlugTest`
- Slug généré automatiquement depuis le titre à la création
- Slug manuel respecté (pas écrasé)
- Caractères spéciaux normalisés (`"Ça & Là"` → `"ca-la"`)

### Fonctionnels — `PageControllerTest` (WebTestCase)
- `GET /pages/{slug}` → 200 pour une page publiée
- `GET /pages/{slug-inexistant}` → 404
- `GET /pages/{slug-brouillon}` → 404 (non visible publiquement)
- `GET /pages` → 200, liste les pages publiées

---

## Décisions retenues

- **Bootstrap 5 via CDN** : cohérence avec EasyAdmin, zéro build step
- **Lifecycle callbacks** dans l'entité (pas de bundle externe) : simple, auditable, testable unitairement
- **`TextEditorField`** d'EasyAdmin 5 : évite une dépendance externe pour l'éditeur riche
- **`MenuBuilder` service** : sépare la logique de récupération du menu de l'affichage Twig
