# EasyAdmin + Tests manquants — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Compléter les 4 tests unitaires manquants et câbler tout EasyAdmin (Dashboard + 9 CrudControllers avec restrictions par rôle).

**Architecture:** Dashboard EasyAdmin avec un DashboardController central, un CrudController par entité, et des restrictions de rôle via `configureMenuItems()` et `configureCrud()`. Les tests unitaires couvrent les entités simples restantes.

**Tech Stack:** Symfony 7.4, EasyAdmin 4.x, PHPUnit, PHP 8.2

---

### Task 1 : Tests — CategorieArticle et Tag

**Files:**
- Create: `tests/Entity/CategorieArticleTest.php`
- Create: `tests/Entity/TagTest.php`

- [ ] Écrire `tests/Entity/CategorieArticleTest.php`
- [ ] Écrire `tests/Entity/TagTest.php`
- [ ] Lancer les tests : `php bin/phpunit tests/Entity/CategorieArticleTest.php tests/Entity/TagTest.php`
- [ ] Commit

---

### Task 2 : Tests — Commentaire et CategoriePage

**Files:**
- Create: `tests/Entity/CommentaireTest.php`
- Create: `tests/Entity/CategoriePageTest.php`

- [ ] Écrire `tests/Entity/CommentaireTest.php`
- [ ] Écrire `tests/Entity/CategoriePageTest.php`
- [ ] Lancer les tests : `php bin/phpunit tests/Entity/CommentaireTest.php tests/Entity/CategoriePageTest.php`
- [ ] Commit

---

### Task 3 : EasyAdmin Dashboard

**Files:**
- Create: `src/Controller/Admin/DashboardController.php`

- [ ] Créer le DashboardController avec menu complet
- [ ] Vérifier accès `/admin` dans le navigateur
- [ ] Commit

---

### Task 4 : CrudControllers — User et CategorieArticle

**Files:**
- Create: `src/Controller/Admin/UserCrudController.php`
- Create: `src/Controller/Admin/CategorieArticleCrudController.php`

- [ ] Créer les deux CrudControllers
- [ ] Commit

---

### Task 5 : CrudControllers — Tag et Commentaire

**Files:**
- Create: `src/Controller/Admin/TagCrudController.php`
- Create: `src/Controller/Admin/CommentaireCrudController.php`

- [ ] Créer les deux CrudControllers
- [ ] Commit

---

### Task 6 : CrudControllers — Article

**Files:**
- Create: `src/Controller/Admin/ArticleCrudController.php`

- [ ] Créer ArticleCrudController avec champs titre, contenu, statut, catégorie, tags, auteur, datePublication
- [ ] Commit

---

### Task 7 : CrudControllers — Page et CategoriePage

**Files:**
- Create: `src/Controller/Admin/PageCrudController.php`
- Create: `src/Controller/Admin/CategoriePageCrudController.php`

- [ ] Créer les deux CrudControllers
- [ ] Commit

---

### Task 8 : CrudControllers — Galerie et Image

**Files:**
- Create: `src/Controller/Admin/GalerieCrudController.php`
- Create: `src/Controller/Admin/ImageCrudController.php`

- [ ] Créer les deux CrudControllers
- [ ] Commit

---

### Task 9 : Restrictions par rôle dans le Dashboard

**Files:**
- Modify: `src/Controller/Admin/DashboardController.php`

- [ ] Restreindre les menus selon les rôles (ROLE_ADMIN vs ROLE_REDACTEUR)
- [ ] Lancer tous les tests : `php bin/phpunit`
- [ ] Commit final
