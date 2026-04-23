# Nexo — CMS Minimaliste Éditorial

> Projet réalisé dans le cadre du cahier des charges **CMS DISII** — voir [`cahier_des_charges_CMS_DISII.pdf`](./cahier_des_charges_CMS_DISII.pdf).

Nexo est un système de gestion de contenu (CMS) moderne et épuré conçu avec **Symfony 7.4** et **EasyAdmin 5**. Il privilégie une expérience utilisateur fluide, une typographie soignée et un design éditorial haut de gamme.

---

## 🎨 Aperçu

| Page d'accueil | Console d'administration |
|:---:|:---:|
| ![Page d'accueil](docs/screenshots/home.png) | ![Console admin](docs/screenshots/admin.png) |

---

## 🧱 Stack technique

| Composant | Version | Rôle |
|---|---|---|
| PHP | ≥ 8.2 | Langage serveur |
| Symfony | 7.4 | Framework MVC |
| EasyAdmin Bundle | ^5.0 | Interface d'administration |
| Doctrine ORM | ^3.6 | Mapping objet-relationnel |
| Doctrine Migrations | ^4.0 | Versioning du schéma BDD |
| Twig | ^3 | Moteur de templates |
| MySQL | 8.0 | Base de données |
| Bootstrap | 5.3 | Design system front |
| PHPUnit | ^13 | Tests unitaires & fonctionnels |

---

## ✨ Fonctionnalités clés

- **Gestion des Pages** — arborescence parent/enfant, WYSIWYG, SEO (slug + méta-description), statuts (brouillon / publié / archivé).
- **Blog** — articles catégorisés, tags multiples, commentaires modérés (en attente / approuvé / rejeté), recherche plein texte.
- **Galeries Photos** — galeries catégorisées, upload d'images sécurisé (max 2 Mo, JPEG / PNG / WebP), légendes.
- **Console Admin EasyAdmin 5** — tableau de bord, CRUD complet, filtres avancés, contrôle d'accès par rôle.
- **Sécurité** — authentification Symfony, 3 rôles hiérarchiques (Admin > Rédacteur > Utilisateur), CSRF, protection XSS via Twig, requêtes paramétrées via Doctrine.
- **Design** — thème personnalisé "Violet Électrique" basé sur la police *Inter*.

---

## 📋 Prérequis

- **PHP ≥ 8.2** avec les extensions `ctype`, `iconv`, `pdo_mysql`
- **Composer** 2.x
- **MySQL ≥ 8.0** (ou MariaDB ≥ 10.11)
- **Symfony CLI** (recommandé — <https://symfony.com/download>)

---

## 🛠 Installation et Lancement

1. **Cloner le dépôt et installer les dépendances :**
   ```bash
   git clone https://github.com/PetitPrince808/CMS-Antoine.git
   cd CMS-Antoine
   composer install
   ```

2. **Configurer la base de données** dans `.env` (par défaut MAMP/XAMPP) :
   ```dotenv
   DATABASE_URL="mysql://root:root@127.0.0.1:8889/cms_disii?serverVersion=8.0&charset=utf8mb4"
   ```

3. **Créer la base et appliquer les migrations :**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

4. **Charger les données de démonstration** *(recommandé)* :
   ```bash
   php bin/console app:seed-data
   ```

5. **Lancer le serveur de développement :**
   ```bash
   symfony serve
   ```
   Le site est accessible sur **<http://127.0.0.1:8000>**.

---

## 👥 Comptes de démonstration

Le seed crée deux comptes correspondant aux deux rôles administratifs du CDC :

| Rôle | Email | Mot de passe | Accès |
|---|---|---|---|
| **Administrateur** | `admin@cms-disii.local` | `admin1234` | Tout (y compris modération des commentaires et gestion des utilisateurs) |
| **Rédacteur** | `redacteur@cms-disii.local` | `redac1234` | Pages, articles, galeries — **sans** modération ni gestion des comptes |
| *Utilisateur (visiteur)* | *pas de compte requis* | — | Consultation du site public + soumission de commentaires après inscription |

Connexion depuis **/login** puis redirection automatique vers **/admin**.

---

## 🗺️ URLs principales

### Front public
| URL | Description |
|---|---|
| `/` | Page d'accueil (pages racines + derniers articles) |
| `/pages` | Liste des pages publiées |
| `/pages/{slug}` | Détail d'une page (ex. `/pages/a-propos`) |
| `/blog` | Liste des articles publiés |
| `/blog/{slug}` | Détail d'un article + commentaires |
| `/blog/recherche?q=...` | Recherche plein texte |
| `/galeries` | Liste des galeries |
| `/galeries/{id}` | Détail d'une galerie |

### Espace authentifié
| URL | Description |
|---|---|
| `/login` | Formulaire de connexion |
| `/logout` | Déconnexion |
| `/admin` | Console d'administration EasyAdmin |

---

## 🧪 Tests

Le projet inclut **48 tests** (unitaires + fonctionnels via `WebTestCase`).

```bash
# Préparer la base de test (une seule fois)
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test --no-interaction

# Lancer la suite
php bin/phpunit
```

---

## 🗄️ Modèle de données

```mermaid
erDiagram
    USER ||--o{ ARTICLE : "écrit"
    USER ||--o{ COMMENTAIRE : "rédige"

    CATEGORIE_ARTICLE ||--o{ ARTICLE : "classe"
    TAG }o--o{ ARTICLE : "étiquette"
    ARTICLE ||--o{ COMMENTAIRE : "reçoit"

    CATEGORIE_PAGE ||--o{ PAGE : "classe"
    PAGE ||--o{ PAGE : "parent / enfant"
    PAGE }o--o| GALERIE : "illustre"

    CATEGORIE_GALERIE ||--o{ GALERIE : "classe"
    GALERIE ||--o{ IMAGE : "contient"

    USER {
        int id PK
        string email UK
        string password
        string nom
        json roles
    }
    PAGE {
        int id PK
        string titre
        string slug UK
        text paragraphes
        string metaDescription
        string statut
        datetime createdAt
        datetime updatedAt
    }
    CATEGORIE_PAGE {
        int id PK
        string nom
    }
    ARTICLE {
        int id PK
        string titre
        text contenu
        string slug UK
        string metaDescription
        string statut
        datetime datePublication
    }
    CATEGORIE_ARTICLE {
        int id PK
        string nom
    }
    TAG {
        int id PK
        string nom UK
    }
    COMMENTAIRE {
        int id PK
        text contenu
        datetime date
        string statut
    }
    GALERIE {
        int id PK
        string nom
        string description
    }
    CATEGORIE_GALERIE {
        int id PK
        string nom
    }
    IMAGE {
        int id PK
        string url
        string legende
        datetime addedAt
    }
```

> Le diagramme est rendu nativement par GitHub / GitLab grâce à Mermaid.

---

## 📐 Couverture du cahier des charges

| Exigence CDC | Implémentation |
|---|---|
| **§3.1 Auth Symfony + 3 rôles** | `security.yaml` + hiérarchie `ROLE_ADMIN > ROLE_REDACTEUR > ROLE_USER` |
| **§3.2 CRUD Pages + WYSIWYG** | `PageCrudController` avec `TextEditorField` |
| **§3.2 SEO friendly** | Slug unique auto-généré + champ `metaDescription` |
| **§3.2 Arborescence pages** | Relation récursive `parent / children` sur `Page` |
| **§3.3 CRUD Articles + catégories** | `Article` ↔ `CategorieArticle` (N-1) |
| **§3.3 Modération commentaires** | Statuts `en_attente` / `approuve` / `rejete`, `CommentaireCrudController` réservé à `ROLE_ADMIN` |
| **§3.3 Tags et métadonnées** | `Tag` (N-N) + `metaDescription` + `datePublication` |
| **§3.4 Galeries avec catégories** | `Galerie` ↔ `CategorieGalerie` (N-1) |
| **§3.4 Upload sécurisé** | Contrainte `Assert\File` : 2 Mo max, JPEG / PNG / WebP |
| **§3.4 Légendes images** | Champ `legende` sur `Image` |
| **§3.5 Tableau de bord EasyAdmin** | `DashboardController` + menu structuré en sections |
| **§3.5 Filtres et recherche** | `configureFilters()` sur Article, Page, Commentaire |
| **§3.6 Protection XSS / SQL / CSRF** | Twig auto-escape + Doctrine paramétré + `csrf.yaml` activé |
| **§3.6 Cache** | Cache Doctrine (query / result) activé en env `prod` |
| **§4.1 Stack technique** | Symfony 7.4, EasyAdmin 5, Doctrine 3, Twig 3, Bootstrap 5 |
| **§4.2 Entités relationnelles** | User, Page, Article, Galerie, Image, Commentaire, Tag + 3 catégories |
| **§4.3 Versioning Git** | Repo GitHub public |
| **§5 Livrables** | Code sur Git, seed (`app:seed-data`), guide de déploiement (ci-dessous) |

---

## 🚢 Déploiement en production

### 1. Configuration d'environnement

Créez un fichier `.env.local` non versionné à la racine :

```dotenv
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=<générer via : openssl rand -hex 16>
DATABASE_URL="mysql://user:password@host:3306/cms_disii?serverVersion=8.0&charset=utf8mb4"
```

### 2. Installation optimisée

```bash
# Dépendances (sans les paquets de dev, autoload optimisé)
composer install --no-dev --optimize-autoloader --classmap-authoritative

# Compilation du cache Symfony en mode prod
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod

# Application des migrations en base
php bin/console doctrine:migrations:migrate --env=prod --no-interaction

# (Optionnel) Chargement du contenu de démonstration
php bin/console app:seed-data --env=prod
```

### 3. Permissions

L'utilisateur du serveur web (ex. `www-data`) doit pouvoir écrire dans :

```bash
chown -R www-data:www-data var/ public/uploads/
chmod -R 775 var/ public/uploads/
```

### 4. Exemple de VirtualHost Apache

```apache
<VirtualHost *:80>
    ServerName cms-nexo.example.com
    DocumentRoot /var/www/cms-nexo/public

    <Directory /var/www/cms-nexo/public>
        AllowOverride None
        Require all granted
        FallbackResource /index.php
    </Directory>

    # Refuse l'accès direct à tout sauf index.php
    <Directory /var/www/cms-nexo/public/bundles>
        FallbackResource disabled
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/cms-nexo_error.log
    CustomLog ${APACHE_LOG_DIR}/cms-nexo_access.log combined
</VirtualHost>
```

Pour Nginx, voir la doc officielle Symfony : <https://symfony.com/doc/current/setup/web_server_configuration.html>

### 5. Checklist de mise en ligne

- [ ] `APP_SECRET` régénéré et différent de la valeur de dev
- [ ] `APP_ENV=prod` et `APP_DEBUG=0`
- [ ] HTTPS actif (certificat Let's Encrypt recommandé)
- [ ] Mot de passe du compte admin modifié depuis `/admin`
- [ ] `var/` et `public/uploads/` writables par le serveur web
- [ ] Sauvegarde planifiée de la base MySQL (ex. `mysqldump` en cron)

---

*Projet réalisé par Antoine — Chartres, 2026.*
