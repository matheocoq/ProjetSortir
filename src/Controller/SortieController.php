<?php

namespace App\Controller;

use App\Repository\SortiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SortieController extends AbstractController
{
    #[Route('/', name: 'sortie_liste')]
    public function liste(SortiesRepository $sortiesRepository): Response
    {  
        $sorties = $sortiesRepository->findAll();
        return $this->render('sortie/sortieListe.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    #[Route('/sortie/create', name: 'sortie_create')]
    public function create(): Response
    {
        return $this->render('sortie/sortieCreate.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }
}
