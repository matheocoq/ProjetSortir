<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Form\SortiesType;
use App\Repository\EtatsRepository;
use App\Repository\SortiesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

        $sortie = new Sorties();
        $sortieForm = $this->createForm(SortiesType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setOrganisateur($userRepository->find(1));
            $sortie->setEtat($etatsRepository->find(1));
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('succes', 'Sortie added !');
            return $this->redirectToRoute('sortie_create');
        }

        return $this->render('sortie/sortieCreate.html.twig', [
            'controller_name' => 'SortieController',
            'sortieForm' => $sortieForm->createView()
        ]);
    }
}
