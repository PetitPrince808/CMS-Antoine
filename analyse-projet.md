# Analyse du projet CMS DISII — Symfony + EasyAdmin

## Vue d'ensemble

Un CMS maison développé avec Symfony, administré via **EasyAdmin**. Le but : gérer des pages statiques, un blog et des galeries photos, avec un système d'authentification par rôles.

---

## Les 3 rôles utilisateurs

| Rôle | Droits |
|---|---|
| **Administrateur** | Tout gérer (utilisateurs, contenus, modération) |
| **Rédacteur** | Créer/modifier pages et articles |
| **Utilisateur** | Consultation publique uniquement |

---

## Les modules à développer

### 1. Gestion des pages
- CRUD complet (Create, Read, Update, Delete)
- Éditeur WYSIWYG (type TinyMCE ou CKEditor)
- URL personnalisables + méta-descriptions (SEO)
- Organisation en arborescence (pages parentes/enfantes)

### 2. Blog
- CRUD des articles
- Catégories + tags
- Commentaires avec modération
- Métadonnées SEO

### 3. Galeries photos
- Galeries avec catégories
- Upload d'images sécurisé
- Légendes sur les images

### 4. Administration EasyAdmin
- Dashboard de synthèse
- Gestion centralisée de tout
- Filtres et recherche

---

## Le MCD (modèle de données)

```
Utilisateur         → ID, Nom, Email, Mot de passe, Rôle
├── Article         → ID, Titre, Contenu, Date publication, Catégorie, Tags, Méta-description, Statut modération
│   ├── CatégorieArticle → ID, Nom
│   ├── Tag         → ID, Nom
│   └── Commentaire → ID, Contenu, Date, Statut

Page                → ID, Titre, Paragraphes, URL, Méta-description, Dates, Statut modération, Galerie
├── CatégoriePage   → ID, Nom
└── Galerie         → ID, Nom, Description
    └── Image       → ID, URL, Légende, Date d'ajout
```

**Relations clés :**
- Un **Utilisateur** peut écrire plusieurs **Articles**
- Un **Article** appartient à une **CatégorieArticle** et peut avoir plusieurs **Tags**
- Un **Article** peut avoir plusieurs **Commentaires**
- Une **Page** peut être associée à une **Galerie**
- Une **Galerie** contient plusieurs **Images**

---

## Stack technique

| Brique | Rôle |
|---|---|
| **Symfony** (dernière stable) | Framework principal |
| **EasyAdmin** | Interface d'admin |
| **Doctrine** | ORM / gestion BDD |
| **Twig** | Templates front |
| **Bootstrap ou Tailwind** | CSS |
| **MySQL/PostgreSQL** | Base de données |
| **Git** | Versioning |

---

## Points d'attention pour le code (niveau débutant)

- **Commentaires ciblés** : expliquer le *pourquoi* d'un choix, pas le *quoi* (le code se lit tout seul). Par exemple, expliquer pourquoi on utilise `#[IsGranted]` sur un contrôleur, pas juste redire "cette ligne vérifie le rôle".
- **Nommer clairement** les entités, contrôleurs et services en anglais
- **Séparer les responsabilités** : chaque classe fait une seule chose
- **Pas de logique métier dans Twig** : Twig = affichage uniquement

---

## Livrables attendus

1. Code source sur Git (repo : `cms-disii-Antoine`)
2. Base de données préconfigurée (migrations Doctrine)
3. Guide de déploiement

---

## Ordre de développement recommandé

1. **Setup** : Symfony + EasyAdmin + Doctrine + auth
2. **Entités** : créer toutes les entités + migrations
3. **Gestion utilisateurs** : rôles + sécurité
4. **Pages** : CRUD + WYSIWYG
5. **Blog** : articles + catégories + tags + commentaires
6. **Galeries** : upload + images
7. **Dashboard EasyAdmin** : tout câbler
8. **Front** : templates Twig + Bootstrap/Tailwind
