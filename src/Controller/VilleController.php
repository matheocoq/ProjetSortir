<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;



class VilleController extends AbstractController
{
    #[Route('/ville/liste', name: 'ville_liste')]
    public function liste(VilleRepository $villeRepository): Response
    {
        $ville= $villeRepository->findAll();
        return $this->render('ville/villeListe.html.twig', [
            'villes' => $ville,
        ]);
    }

    #[Route('/ville/create', name: 'ville_create')]
    public function create(EntityManagerInterface $entityManager,Request $request, VilleRepository $villeRepository): Response
    {
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class,$ville);
        $villeForm->handleRequest($request);

        if($villeForm->isSubmitted() && $villeForm->isValid()){
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('succes', 'ville crée !');
            return $this->redirectToRoute('ville_liste');
        }
        
        return $this->render('ville/villeCreate.html.twig', [
            'villeForm' => $villeForm->createView(),
            'ville' => $ville,
        ]);
    }

    #[Route('/ville/update/{id}', name: 'ville_update')]
    public function update(EntityManagerInterface $entityManager,Request $request,VilleRepository $villeRepository,Ville $ville): Response
    {
       
        $villeForm = $this->createForm(VilleType::class,$ville);
        $villeForm->handleRequest($request);

        if($villeForm->isSubmitted() && $villeForm->isValid()){
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('succes', 'ville modifier !');
            return $this->redirectToRoute('ville_liste');
        }
        
        return $this->render('ville/villeUpdate.html.twig', [
            'villeForm' => $villeForm->createView(),
            'ville' => $ville,
        ]);
    }

    #[Route('/ville/delete/{id}', name: 'ville_delete')]
    public function delete(VilleRepository $villeRepository,Ville $ville): Response
    {
        $user = $this->getUser();
        $messageSucces = '';
        if($user->getId()){
            
        }
        $this->addFlash('succes', $messageSucces);
        return $this->redirectToRoute("sortie_liste");
    }
}
