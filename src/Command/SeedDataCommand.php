<?php

namespace App\Command;

use App\Entity\Article;
use App\Entity\CategorieArticle;
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
        // 1. Utilisateurs
        $admin = $this->em->getRepository(User::class)->findOneBy(['email' => 'admin@cms-disii.local']);
        if (!$admin) {
            $admin = new User();
            $admin->setEmail('admin@cms-disii.local');
            $admin->setNom('Administrateur');
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setPassword($this->hasher->hashPassword($admin, 'admin1234'));
            $this->em->persist($admin);
        }

        // 2. Catégories & Tags (Vérification existence)
        $catTech = $this->em->getRepository(CategorieArticle::class)->findOneBy(['nom' => 'Technologie']) ?? new CategorieArticle();
        $catTech->setNom('Technologie'); $this->em->persist($catTech);

        $catDesign = $this->em->getRepository(CategorieArticle::class)->findOneBy(['nom' => 'Design']) ?? new CategorieArticle();
        $catDesign->setNom('Design'); $this->em->persist($catDesign);

        $tagSymfony = $this->em->getRepository(Tag::class)->findOneBy(['nom' => 'Symfony']) ?? new Tag();
        $tagSymfony->setNom('Symfony'); $this->em->persist($tagSymfony);

        $tagMinimal = $this->em->getRepository(Tag::class)->findOneBy(['nom' => 'Minimalisme']) ?? new Tag();
        $tagMinimal->setNom('Minimalisme'); $this->em->persist($tagMinimal);

        // 3. Articles (Vérification par titre)
        $articlesData = [
            [
                'titre' => 'Le futur du développement web en 2026',
                'contenu' => '<p>Le Web évolue vers plus de simplicité. Les frameworks deviennent plus légers et l\'IA aide à coder plus vite sans sacrifier la qualité.</p>',
                'cat' => $catTech,
                'tags' => [$tagSymfony]
            ],
            [
                'titre' => 'Pourquoi le minimalisme gagne toujours',
                'contenu' => '<p>Moins, c\'est mieux. Dans un monde saturé d\'informations, le design minimaliste permet à l\'utilisateur de se concentrer sur l\'essentiel.</p>',
                'cat' => $catDesign,
                'tags' => [$tagMinimal]
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
        $pageApropos->setTitre('A propos');
        $pageApropos->setParagraphes('
            <p class="lead">Né en 2026, CMS Nexo est le fruit d\'une réflexion sur la surcharge numérique. Notre mission est de redéfinir la gestion de contenu pour les créateurs qui privilégient la clarté et l\'efficacité.</p>
            
            <h3 class="mt-4">Notre Philosophie</h3>
            <p>Nous croyons que la technologie ne doit pas être un obstacle, mais un catalyseur. C\'est pourquoi nous avons conçu un outil qui élimine le superflu pour se concentrer sur l\'essentiel : <strong>votre message</strong>.</p>
            
            <div class="row mt-5">
                <div class="col-md-4">
                    <h5 class="fw-bold">Performance</h5>
                    <p class="small text-muted">Bâti sur Symfony 7.4, Nexo offre une rapidité d\'exécution sans compromis.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold">Minimalisme</h5>
                    <p class="small text-muted">Une interface épurée, sans distraction, inspirée des meilleurs standards.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold">Liberté</h5>
                    <p class="small text-muted">Un système modulaire qui s\'adapte à vos besoins, personnels ou professionnels.</p>
                </div>
            </div>
            
            <h3 class="mt-5">L\'Équipe</h3>
            <p>Basée à Chartres, notre équipe de passionnés travaille chaque jour pour faire évoluer cet écosystème en restant fidèle à nos valeurs de simplicité et de robustesse technique.</p>
        ');
        $pageApropos->setStatut('publie');
        $pageApropos->setCategoriePage($catInfo);
        $pageApropos->setMetaDescription('Découvrez la philosophie et l\'équipe derrière le CMS Nexo, votre outil de gestion de contenu minimaliste.');
        $this->em->persist($pageApropos);

        // 5. Galerie & Images (Purge et recréation pour les photos)
        $galerie = $this->em->getRepository(Galerie::class)->findOneBy(['nom' => 'Nos bureaux']);
        if (!$galerie) {
            $galerie = new Galerie();
            $galerie->setNom('Nos bureaux');
            $galerie->setDescription('Un aperçu de notre espace de travail créatif.');
            $this->em->persist($galerie);
        } else {
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
