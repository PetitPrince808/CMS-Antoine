<?php

namespace App\Command;

use App\Entity\Article;
use App\Entity\CategorieArticle;
use App\Entity\CategorieGalerie;
use App\Entity\CategoriePage;
use App\Entity\Commentaire;
use App\Entity\Galerie;
use App\Entity\Image;
use App\Entity\Page;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:seed-data', description: 'Remplit la base de données avec des contenus de test')]
class SeedDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 1. Utilisateurs (admin + rédacteur de démonstration)
        $admin = $this->em->getRepository(User::class)->findOneBy(['email' => 'admin@cms-disii.local']);
        if (!$admin) {
            $admin = new User();
            $admin->setEmail('admin@cms-disii.local');
            $admin->setNom('Administrateur');
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setPassword($this->hasher->hashPassword($admin, 'admin1234'));
            $this->em->persist($admin);
        }

        $redacteur = $this->em->getRepository(User::class)->findOneBy(['email' => 'redacteur@cms-disii.local']);
        if (!$redacteur) {
            $redacteur = new User();
            $redacteur->setEmail('redacteur@cms-disii.local');
            $redacteur->setNom('Rédacteur démo');
            $redacteur->setRoles(['ROLE_REDACTEUR']);
            $redacteur->setPassword($this->hasher->hashPassword($redacteur, 'redac1234'));
            $this->em->persist($redacteur);
        }

        // 2. Catégories & Tags (Vérification existence)
        $catTech = $this->em->getRepository(CategorieArticle::class)->findOneBy(['nom' => 'Technologie']) ?? new CategorieArticle();
        $catTech->setNom('Technologie'); $this->em->persist($catTech);

        $catDesign = $this->em->getRepository(CategorieArticle::class)->findOneBy(['nom' => 'Design']) ?? new CategorieArticle();
        $catDesign->setNom('Design'); $this->em->persist($catDesign);

        $catActualite = $this->em->getRepository(CategorieArticle::class)->findOneBy(['nom' => 'Actualité']) ?? new CategorieArticle();
        $catActualite->setNom('Actualité'); $this->em->persist($catActualite);

        $tagSymfony = $this->em->getRepository(Tag::class)->findOneBy(['nom' => 'Symfony']) ?? new Tag();
        $tagSymfony->setNom('Symfony'); $this->em->persist($tagSymfony);

        $tagMinimal = $this->em->getRepository(Tag::class)->findOneBy(['nom' => 'Minimalisme']) ?? new Tag();
        $tagMinimal->setNom('Minimalisme'); $this->em->persist($tagMinimal);

        $tagWeb = $this->em->getRepository(Tag::class)->findOneBy(['nom' => 'Web']) ?? new Tag();
        $tagWeb->setNom('Web'); $this->em->persist($tagWeb);

        $tagIA = $this->em->getRepository(Tag::class)->findOneBy(['nom' => 'IA']) ?? new Tag();
        $tagIA->setNom('IA'); $this->em->persist($tagIA);

        // 3. Articles (Vérification par titre)
        $articlesData = [
            [
                'titre' => 'Le futur du développement web en 2026',
                'contenu' => '<p>Le Web évolue vers plus de simplicité. Les frameworks deviennent plus légers et l\'IA aide à coder plus vite sans sacrifier la qualité.</p><p>Les architectures distribuées et le Edge Computing permettent des temps de réponse quasi instantanés pour les utilisateurs du monde entier.</p>',
                'cat' => $catTech,
                'tags' => [$tagSymfony, $tagWeb]
            ],
            [
                'titre' => 'Pourquoi le minimalisme gagne toujours',
                'contenu' => '<p>Moins, c\'est mieux. Dans un monde saturé d\'informations, le design minimaliste permet à l\'utilisateur de se concentrer sur l\'essentiel.</p><p>L\'esthétique épurée ne se limite pas au visuel, elle concerne aussi l\'expérience utilisateur et la performance technique.</p>',
                'cat' => $catDesign,
                'tags' => [$tagMinimal]
            ],
            [
                'titre' => 'L\'impact de l\'IA sur la création de contenu',
                'contenu' => '<p>L\'intelligence artificielle générative transforme la manière dont nous produisons des textes, des images et des vidéos.</p><p>Les créateurs doivent apprendre à collaborer avec ces outils pour augmenter leur productivité tout en conservant une touche humaine unique.</p>',
                'cat' => $catTech,
                'tags' => [$tagIA, $tagWeb]
            ],
            [
                'titre' => 'Nouvelle version de CMS Nexo disponible',
                'contenu' => '<p>Nous sommes fiers d\'annoncer la sortie de la version 2.0 de CMS Nexo. Cette mise à jour apporte une interface encore plus fluide et de nouveaux modules de personnalisation.</p>',
                'cat' => $catActualite,
                'tags' => [$tagWeb]
            ]
        ];

        foreach ($articlesData as $data) {
            $article = $this->em->getRepository(Article::class)->findOneBy(['titre' => $data['titre']]) ?? new Article();
            $article->setTitre($data['titre']);
            $article->setContenu($data['contenu']);
            $article->setStatut('publie');
            $article->setDatePublication(new \DateTime());
            $article->setCategorieArticle($data['cat']);
            $article->setAuteur($admin);
            foreach ($data['tags'] as $tag) $article->addTag($tag);
            $this->em->persist($article);
        }

        // 4. Page A propos
        $catInfo = $this->em->getRepository(CategoriePage::class)->findOneBy(['nom' => 'Informations']) ?? new CategoriePage();
        $catInfo->setNom('Informations');
        $this->em->persist($catInfo);

        $pageApropos = $this->em->getRepository(Page::class)->findOneBy(['slug' => 'a-propos']) ?? new Page();
        $pageApropos->setTitre('À propos de Nexo');
        $pageApropos->setParagraphes('
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <span class="badge bg-light text-primary mb-3">Depuis 2026</span>
                    <h2 class="display-5 fw-bold mb-4">Redéfinir la clarté numérique.</h2>
                    <p class="lead text-muted">CMS Nexo est né d\'une ambition simple : libérer les créateurs de la complexité technique pour qu\'ils se concentrent sur ce qui compte vraiment — leur message.</p>
                </div>
                <div class="col-lg-5 offset-lg-1 d-none d-lg-block">
                    <img src="https://images.unsplash.com/photo-1499951360447-b19be8fe80f5?auto=format&fit=crop&w=800&q=80" class="img-fluid rounded-4 shadow-lg" alt="Design minimaliste">
                </div>
            </div>
            
            <div class="bg-light p-5 rounded-5 my-5">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="p-3">
                            <div class="h3 text-primary mb-3">01.</div>
                            <h4 class="fw-bold">Performance</h4>
                            <p class="text-muted small">Bâti sur Symfony 7.4, Nexo offre une rapidité d\'exécution sans compromis, garantissant une expérience fluide sur tous les appareils.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <div class="h3 text-primary mb-3">02.</div>
                            <h4 class="fw-bold">Minimalisme</h4>
                            <p class="text-muted small">Une interface épurée, inspirée des meilleurs standards du design moderne, pour une gestion de contenu sans distraction.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <div class="h3 text-primary mb-3">03.</div>
                            <h4 class="fw-bold">Liberté</h4>
                            <p class="text-muted small">Un système modulaire et extensible qui s\'adapte précisément à votre vision, que vous soyez blogueur ou entrepreneur.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h3 class="fw-bold mb-4">Notre Philosophie</h3>
                    <p class="mb-5">Nous croyons que la technologie ne doit pas être un obstacle, mais un catalyseur. Chaque ligne de code de Nexo est écrite avec le souci du détail, de la sécurité et de la pérennité.</p>
                    <hr class="w-25 mx-auto mb-5">
                    <p class="small text-uppercase fw-bold ls-widest text-muted">L\'Équipe Nexo — Chartres, France</p>
                </div>
            </div>
        ');
        $pageApropos->setStatut('publie');
        $pageApropos->setCategoriePage($catInfo);
        $pageApropos->setMetaDescription('Découvrez la philosophie et l\'équipe derrière le CMS Nexo, votre outil de gestion de contenu minimaliste.');
        $this->em->persist($pageApropos);

        // 5. Catégorie de galerie
        $catGalerieBureaux = $this->em->getRepository(CategorieGalerie::class)->findOneBy(['nom' => 'Bureaux']) ?? new CategorieGalerie();
        $catGalerieBureaux->setNom('Bureaux');
        $this->em->persist($catGalerieBureaux);

        $catGalerieEvents = $this->em->getRepository(CategorieGalerie::class)->findOneBy(['nom' => 'Événements']) ?? new CategorieGalerie();
        $catGalerieEvents->setNom('Événements');
        $this->em->persist($catGalerieEvents);

        // 6. Galerie & Images (Purge et recréation pour les photos)
        $galerie = $this->em->getRepository(Galerie::class)->findOneBy(['nom' => 'Nos bureaux']);
        if (!$galerie) {
            $galerie = new Galerie();
            $galerie->setNom('Nos bureaux');
            $galerie->setDescription('Un aperçu de notre espace de travail créatif.');
            $galerie->setCategorie($catGalerieBureaux);
            $this->em->persist($galerie);
        } else {
            $galerie->setCategorie($catGalerieBureaux);
            foreach ($galerie->getImages() as $existingImg) {
                $this->em->remove($existingImg);
            }
            $this->em->flush();
        }

        $officeImages = [
            'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=800&q=80'
        ];

        foreach ($officeImages as $key => $url) {
            $img = new Image();
            $img->setGalerie($galerie);
            $img->setLegende('Espace de travail inspirant #' . ($key + 1));
            $img->setAddedAt(new \DateTime());
            $img->setUrl($url);
            $this->em->persist($img);
        }

        $this->em->flush();
        $output->writeln('Base de données mise à jour avec succès (Images de bureaux thématiques) !');

        return Command::SUCCESS;
    }
}
