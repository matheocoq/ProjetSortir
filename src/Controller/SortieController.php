<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'app_sortie')]
class SortieController extends AbstractController
{
    #[Route('/create', name: 'sortie_create')]
    public function index(): Response
    {
        return $this->render('sortie/sortieCreate.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }
}
