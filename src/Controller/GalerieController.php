<?php

namespace App\Controller;

use App\Repository\GalerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/galeries', name: 'galerie_')]
class GalerieController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(GalerieRepository $galerieRepository): Response
    {
        return $this->render('galerie/index.html.twig', [
            'galeries' => $galerieRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, GalerieRepository $galerieRepository): Response
    {
        $galerie = $galerieRepository->find($id);

        if (!$galerie) {
            throw $this->createNotFoundException('Galerie non trouvée.');
        }

        return $this->render('galerie/show.html.twig', [
            'galerie' => $galerie,
        ]);
    }
}
