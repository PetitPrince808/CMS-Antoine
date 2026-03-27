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

### Tests unitaires
- [x] UserTest
- [x] ArticleTest
- [x] PageTest
- [x] GalerieTest
- [x] ImageTest

---

## ❌ À faire

### Tests manquants
- [ ] CategorieArticleTest
- [ ] TagTest
- [ ] CommentaireTest
- [ ] CategoriePageTest

### EasyAdmin (étape prioritaire)
- [ ] Dashboard
- [ ] CrudController User
- [ ] CrudController Article + CategorieArticle + Tag + Commentaire
- [ ] CrudController Page + CategoriePage
- [ ] CrudController Galerie + Image
- [ ] Restrictions par rôle sur les CRUD

### Pages
- [ ] Installer CKEditor
- [ ] CRUD Pages (front)
- [ ] Gestion slug + SEO
- [ ] Arborescence parent/enfant dans l'admin

### Blog
- [ ] CRUD Articles (front)
- [ ] Gestion catégories + tags
- [ ] Commentaires avec modération

### Galeries
- [ ] Upload d'images sécurisé
- [ ] Affichage galerie avec légendes

### Front
- [ ] Templates Twig de base (layout, nav, footer)
- [ ] Page d'accueil
- [ ] Page article / liste articles
- [ ] Page galerie
