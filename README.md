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

