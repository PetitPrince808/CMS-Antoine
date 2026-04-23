# Nexo — CMS Minimaliste Éditorial

Nexo est un système de gestion de contenu (CMS) moderne et épuré conçu avec **Symfony 7.4**. Il privilégie une expérience utilisateur fluide, une typographie soignée et un design éditorial haut de gamme.

## 🚀 Fonctionnalités clés

- **Gestion des Pages** : Système de pages hiérarchiques (parent/enfant) avec rendu WYSIWYG.
- **Journal (Blog)** : Articles catégorisés, gestion de tags et système de commentaires avec modération.
- **Galeries Photos** : Affichage immersif de projets photographiques avec légendes.
- **Console d'Administration** : Interface puissante propulsée par **EasyAdmin 5** pour piloter l'intégralité du site.
- **Recherche Intégrée** : Barre de recherche performante pour retrouver instantanément les articles.
- **Optimisation SEO** : Balises méta dynamiques pour chaque page et article.
- **Design Système** : Thème personnalisé "Violet Électrique" basé sur la police *Inter*.

## 🛠 Installation et Lancement

1. **Installation des dépendances** :
   ```bash
   composer install
   ```

2. **Configuration de la base de données** :
   *Vérifiez vos accès dans le fichier `.env`.*
   ```bash
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

3. **Injection des données de test (Optionnel)** :
   *Pour découvrir le site avec du contenu déjà prêt (recommandé pour la notation) :*
   ```bash
   php bin/console app:seed-data
   ```

4. **Lancement du serveur** :
   ```bash
   symfony serve
   ```
   *Le site sera accessible sur : **http://127.0.0.1:8000***

## 🔐 Accès Administration

Pour accéder à la **Console Nexo** et gérer les contenus :

- **URL** : `/admin` (ou `/login`)
- **Email** : `admin@cms-disii.local`
- **Mot de passe** : `admin1234`

*Note : Seuls les comptes possédant le rôle `ROLE_REDACTEUR` ou `ROLE_ADMIN` peuvent accéder à la console.*

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

