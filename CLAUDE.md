# CLAUDE.md — CMS DISII

## Contexte du projet

CMS maison académique nommé **cms-disii-Antoine**. Stack : Symfony (dernière stable), EasyAdmin, Doctrine ORM, Twig, Bootstrap ou Tailwind, MySQL, PHPUnit.

Repo Git : `cms-disii-Antoine` — branche unique `main`.

---

## Rôles utilisateurs

| Rôle | Droits |
|---|---|
| `ROLE_ADMIN` | Tout gérer (utilisateurs, contenus, modération) |
| `ROLE_REDACTEUR` | Créer/modifier pages et articles |
| `ROLE_USER` | Consultation publique uniquement |

---

## Modules du projet

1. **Pages** : CRUD, WYSIWYG CKEditor, URL personnalisées + méta SEO, arborescence parent/enfant
2. **Blog** : CRUD articles, catégories + tags, commentaires avec modération, SEO
3. **Galeries photos** : galeries catégorisées, upload sécurisé, légendes
4. **EasyAdmin** : dashboard de synthèse, gestion centralisée, filtres/recherche

---

## Entités Doctrine

```
User           → id, nom, email, password, roles[]
Article        → id, titre, contenu, datePublication, categorieArticle, tags[], metaDescription, statut
CategorieArticle → id, nom
Tag            → id, nom
Commentaire    → id, contenu, date, statut, article
Page           → id, titre, paragraphes, slug, metaDescription, createdAt, updatedAt, statut, parent, galerie
CategoriePage  → id, nom
Galerie        → id, nom, description
Image          → id, url, legende, addedAt, galerie
```

---

## Ordre de développement

1. Setup : Symfony + EasyAdmin + Doctrine + auth
2. Entités : créer toutes les entités + migrations
3. Gestion utilisateurs : rôles + sécurité (`#[IsGranted]`)
4. Pages : CRUD + CKEditor
5. Blog : articles + catégories + tags + commentaires
6. Galeries : upload + images
7. Dashboard EasyAdmin : tout câbler
8. Front : templates Twig + Bootstrap/Tailwind

Ne jamais sauter une étape. Toujours terminer le module en cours avant d'en commencer un autre.

---

## Conventions de code

- **Commentaires** : en français, expliquer le *pourquoi* (pas le *quoi*)
- **Nommage** : entités, contrôleurs, services, variables, fonctions → en anglais
- **Séparation des responsabilités** : chaque classe fait une seule chose
- **Twig = affichage uniquement** : aucune logique métier dans les templates
- **Services** : isoler toute logique complexe dans des services dédiés
- **Sécurité** : utiliser `#[IsGranted]` sur les contrôleurs, pas de vérifications manuelles inline

---

## Base de données

- **Moteur** : MySQL (MAMP local)
- **ORM** : Doctrine avec migrations
- **Environnement de test** : base séparée configurée dans `.env.test`
- Toujours générer une migration après chaque modification d'entité

---

## Tests (PHPUnit)

- Générer les tests **en même temps** que le code, jamais après
- Tests fonctionnels pour les contrôleurs (WebTestCase)
- Tests unitaires pour les services
- Lancer les tests avec `php bin/phpunit` avant chaque commit

---

## Git

- Branche unique : `main`
- **Committer et pusher après chaque feature complète**
- Messages de commit : naturels, en français, sans mention d'outil
  - Format : `[Module] Action courte` → ex: `[Blog] Ajout du CRUD articles`
- Ne jamais committer sans que les tests passent

---

## Comportement attendu

- **Toujours montrer un plan** avant de toucher au code
- **Demander confirmation** avant de modifier plusieurs fichiers simultanément
- Proposer **2-3 options** quand une décision technique n'est pas évidente
- Si une décision est incertaine, le dire explicitement plutôt que d'improviser
