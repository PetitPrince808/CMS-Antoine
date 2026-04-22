# Design — Module Blog

**Date :** 2026-04-22  
**Statut :** Validé

---

## Contexte

Le CMS DISII dispose déjà du module Pages (CRUD EasyAdmin + front Bootstrap 5). Le module Blog ajoute la consultation publique des articles, avec soumission de commentaires réservée aux utilisateurs connectés.

---

## Périmètre

- Affichage public des articles publiés (liste + détail)
- Formulaire de commentaire pour les utilisateurs connectés (statut `en_attente` par défaut)
- Lien "Blog" dans la navbar principale
- Aucun CRUD front — la création/modération reste dans EasyAdmin

---

## Composants

### 1. Entité `Article` — ajout du champ `slug`

- Nouveau champ `slug` (string, unique, nullable: false)
- Généré automatiquement depuis le `titre` via `AsciiSlugger` dans un `#[ORM\PrePersist]`
- Migration Doctrine à générer après la modification de l'entité

### 2. `ArticleRepository`

Deux méthodes personnalisées :

| Méthode | Description |
|---|---|
| `findPublished()` | Retourne tous les articles avec `statut = 'publie'`, triés par `datePublication DESC` |
| `findOnePublishedBySlug(string $slug)` | Retourne un article publié par son slug, ou `null` |

### 3. `CommentaireHandler` (service)

Responsabilité unique : valider et persister un commentaire soumis par un utilisateur connecté.

- Reçoit : `Article $article`, `Commentaire $commentaire`, `User $auteur`
- Associe l'auteur et l'article au commentaire
- Force le statut à `en_attente`
- Persiste via `EntityManagerInterface`

### 4. `CommentaireType` (formulaire Symfony)

- Champ unique : `contenu` (textarea, contrainte `NotBlank`, longueur min 5)
- Pas de champ auteur (récupéré depuis la session)

### 5. `ArticleController`

| Route | Méthode | Action |
|---|---|---|
| `GET /blog` | `index()` | Liste des articles publiés |
| `GET /blog/{slug}` | `show()` | Détail + commentaires approuvés + formulaire |
| `POST /blog/{slug}/commenter` | `comment()` | Soumission commentaire — `#[IsGranted('IS_AUTHENTICATED_FULLY')]` |

Le formulaire de commentaire est rendu dans `show.html.twig` uniquement si `is_granted('IS_AUTHENTICATED_FULLY')`.

**Flux POST commentaire :**
1. Valider le formulaire `CommentaireType`
2. Déléguer à `CommentaireHandler::submit()`
3. Ajouter un flash message (`success` ou `error`)
4. Rediriger vers `GET /blog/{slug}`

### 6. Templates Twig

**`blog/index.html.twig`**
- Liste des articles publiés : titre (lien vers show), date, catégorie (badge), tags (badges), extrait du contenu (150 premiers caractères)
- Message "Aucun article publié" si liste vide

**`blog/show.html.twig`**
- Titre, date, auteur, catégorie + tags
- Contenu complet (raw pour HTML éventuel)
- Section commentaires : liste des commentaires avec `statut = 'approuve'`
- Formulaire de commentaire si utilisateur connecté, sinon message "Connectez-vous pour commenter"

### 7. Navbar

Ajout d'un lien fixe "Blog" dans `base.html.twig`, après les liens de pages dynamiques.

---

## Sécurité

- `#[IsGranted('IS_AUTHENTICATED_FULLY')]` sur `ArticleController::comment()`
- Statut `en_attente` systématique à la création — la modération se fait dans EasyAdmin
- Seuls les commentaires `approuve` sont affichés en front

---

## Tests

| Fichier | Type | Ce qui est testé |
|---|---|---|
| `CommentaireHandlerTest` | Unitaire | submit() : auteur associé, statut en_attente, persist appelé |
| `ArticleControllerTest` | Fonctionnel (WebTestCase) | GET /blog (200), GET /blog/{slug} (200 et 404), POST /blog/{slug}/commenter sans auth (redirect login), POST avec auth (redirect + flash) |

---

## Ordre d'implémentation

1. Ajout `slug` dans `Article` + migration
2. `ArticleRepository` (findPublished, findOnePublishedBySlug)
3. `CommentaireHandler` + `CommentaireHandlerTest`
4. `CommentaireType`
5. `ArticleController`
6. Templates `blog/index.html.twig` et `blog/show.html.twig`
7. Mise à jour `base.html.twig` (lien Blog navbar)
8. `ArticleControllerTest`
9. Commit
