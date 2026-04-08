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
- [x] PageTest + PageSlugTest
- [x] GalerieTest
- [x] ImageTest
- [x] CategorieArticleTest
- [x] TagTest
- [x] CommentaireTest
- [x] CategoriePageTest

### EasyAdmin
- [x] Dashboard
- [x] CrudController User
- [x] CrudController Article + CategorieArticle + Tag + Commentaire
- [x] CrudController Page + CategoriePage
- [x] CrudController Galerie + Image
- [x] Restrictions par rôle sur les CRUD

### Pages
- [x] Slug auto-généré (AsciiSlugger, lifecycle callbacks)
- [x] Éditeur riche dans l'admin (TextEditorField EasyAdmin 5)
- [x] Contrôleur front + templates Bootstrap 5
- [x] Arborescence parent/enfant (fil d'ariane + sous-pages)

---

## ❌ À faire

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
