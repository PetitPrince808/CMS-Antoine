# Design — Module Galeries

**Date :** 2026-04-22  
**Statut :** Validé

---

## Contexte

Les entités `Galerie` et `Image` existent déjà. EasyAdmin gère l'upload via `ImageCrudController` (`ImageField` configuré vers `public/uploads/galeries/`). Il manque uniquement le front-end public : liste des galeries et affichage d'une galerie avec ses images.

---

## Périmètre

- Affichage public de toutes les galeries (liste)
- Affichage d'une galerie avec sa grille d'images (détail)
- Lien "Galeries" dans la navbar principale
- Création du dossier `public/uploads/galeries/` pour activer l'upload EasyAdmin
- Aucun formulaire front — la gestion reste dans EasyAdmin

---

## Composants

### 1. `GalerieController`

| Route | Méthode | Action |
|---|---|---|
| `GET /galeries` | `index()` | Liste toutes les galeries |
| `GET /galeries/{id}` | `show()` | Détail d'une galerie avec ses images |

- `index()` : utilise `GalerieRepository::findAll()` (méthode Doctrine native)
- `show()` : utilise `GalerieRepository::find($id)` → 404 si null

Pas de `GalerieRepository` custom — les méthodes Doctrine natives suffisent.

### 2. `templates/galerie/index.html.twig`

- Liste des galeries en cards Bootstrap 5
- Chaque card : nom de la galerie (lien vers show), description, nombre d'images
- Message "Aucune galerie" si liste vide

### 3. `templates/galerie/show.html.twig`

- Nom et description de la galerie en en-tête
- Grille d'images : `row-cols-2 row-cols-md-3 g-3` (Bootstrap 5)
- Chaque image : balise `<img>` avec `src="{{ asset(image.url) }}"`, légende si présente
- Message "Aucune image" si la galerie est vide

### 4. `templates/base.html.twig`

Ajout d'un lien "Galeries" dans la navbar après le lien "Blog".

### 5. `public/uploads/galeries/`

Dossier à créer pour que EasyAdmin puisse écrire les fichiers uploadés. Un fichier `.gitkeep` est ajouté pour versionner le dossier vide.

---

## Sécurité

Accès public — toutes les galeries sont visibles sans authentification, conformément au rôle `ROLE_USER` (consultation publique).

---

## Tests

| Fichier | Type | Ce qui est testé |
|---|---|---|
| `tests/Controller/GalerieControllerTest.php` | Fonctionnel (WebTestCase) | GET /galeries (200), GET /galeries/999 (404) |

---

## Ordre d'implémentation

1. Créer `public/uploads/galeries/.gitkeep`
2. Créer `GalerieController` (index + show)
3. Créer `templates/galerie/index.html.twig`
4. Créer `templates/galerie/show.html.twig`
5. Mettre à jour `templates/base.html.twig` (lien Galeries)
6. Écrire `GalerieControllerTest`
7. Commit
