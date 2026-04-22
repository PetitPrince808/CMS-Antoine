# Todo — CMS DISII

## ✅ Fait

### Setup
- [x] Symfony 7.4 + EasyAdmin + Doctrine + Auth installés
- [x] Base de données MySQL configurée
- [x] Migration initiale (toutes les tables)

### Entités
- [x] User
- [x] Article + CategorieArticle + Tag + Commentaire
- [x] Page + CategoriePage
- [x] Galerie + Image

### Auth
- [x] Hiérarchie de rôles (ROLE_USER → ROLE_REDACTEUR → ROLE_ADMIN)
- [x] Login / Logout
- [x] Accès `/admin/*` restreint à ROLE_REDACTEUR
- [x] Commande `app:create-admin` pour le développement

### Tests unitaires & fonctionnels
- [x] Tests Entités (User, Article, Page, etc.)
- [x] Tests Contrôleurs (Blog, Galerie, Page)

### EasyAdmin
- [x] Dashboard de synthèse
- [x] Gestion de toutes les entités
- [x] Upload d'images sécurisé avec prévisualisation
- [x] Restrictions par rôle sur les CRUD

### Front-office (Templates Twig + Bootstrap 5)
- [x] Navigation dynamique (MenuBuilder)
- [x] Pages : arborescence, fil d'ariane, rendu WYSIWYG
- [x] Blog : listing, lecture, recherche par mot-clé
- [x] Galeries : affichage en grille avec légendes
- [x] Commentaires : soumission front + modération back
- [x] SEO : balises méta dynamiques (description, titres)
- [x] Accueil : design moderne avec mise en avant des derniers articles

---

## 🚀 Améliorations futures possibles
- [ ] Système de newsletter
- [ ] Export PDF des articles
- [ ] Interface multilingue
